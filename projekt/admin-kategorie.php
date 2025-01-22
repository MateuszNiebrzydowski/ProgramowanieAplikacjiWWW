<?php


function handleCategorySubmissions($link)
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category']) && isset($_GET['category_id'])) {
    $id = filter_var($_GET['category_id'], FILTER_VALIDATE_INT);

    if ($id === false) {
      die("Invalid category ID");
    }

    $nazwa = mysqli_real_escape_string($link, trim($_POST['category_name']));
    $matka = !empty($_POST['parent_category']) ?
      mysqli_real_escape_string($link, trim($_POST['parent_category'])) : 0;

    $query = "UPDATE category SET matka = $matka, nazwa = '$nazwa' WHERE id = $id LIMIT 1";
    mysqli_query($link, $query);
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $nazwa = mysqli_real_escape_string($link, trim($_POST['category_name']));
    $matka = !empty($_POST['matka']) ? $_POST['matka'] : 0;

    $query = "INSERT INTO category (matka, nazwa) VALUES ($matka, '$nazwa')";
    mysqli_query($link, $query);
  }


  if (isset($_GET['action']) && $_GET['action'] === 'delete_category' && isset($_GET['category_id'])) {
    $id = filter_var($_GET['category_id'], FILTER_VALIDATE_INT);

    if ($id === false) {
      die("Invalid category ID");
    }

    $query = "DELETE FROM category WHERE id = ? LIMIT 1";
    mysqli_query($link, $query);


  }
}


function ListaKategorii($link)
{
  $result = '<div class="card">';
  $result .= '<h2>Lista Kategorii</h2>';
  $result .= '<a href="?action=add_category" class="btn btn-success">Dodaj nową kategorię</a>';


  $categories = [];
  $query = "SELECT * FROM category ORDER BY nazwa ASC";
  $categoriesResult = mysqli_query($link, $query);

  while ($row = mysqli_fetch_array($categoriesResult)) {
    $parentId = $row['matka'] === NULL ? '0' : $row['matka'];
    $categories[$parentId][] = $row;
  }

  $result .= '<div class="category-tree">';
  $result .= displayCategoryTree($categories);
  $result .= '</div>';


  $result .= '</div>';
  return $result;
}

function displayCategoryTree($categories, $parentId = '0', $level = 0)
{
  if (!isset($categories[$parentId])) {
    return '';
  }

  $output = '<ul class="category-list' . ($level === 0 ? ' root-level' : '') . '">';
  foreach ($categories[$parentId] as $category) {
    $hasChildren = isset($categories[$category['id']]);

    $output .= '<li class="category-item' . ($hasChildren ? ' has-children' : '') . '">';
    $output .= '<div class="category-content">';
    $output .= '<div class="category-main">';
    
    $output .= '<span class="category-name">' . htmlspecialchars($category['nazwa']) . '</span>';
    $output .= '</div>';

    $output .= '<div class="category-actions">';
    $output .= '<a href="?action=edit_category&category_id=' . htmlspecialchars($category['id']) . '" 
                  class="btn btn-primary btn-sm">Edytuj  </a>';
    $output .= '<a href="?action=delete_category&category_id=' . htmlspecialchars($category['id']) . '" 
                  class="btn btn-danger btn-sm" 
                  onclick="return confirm(\'Czy na pewno chcesz usunąć tę kategorię?\')">Usuń</a>';
    $output .= '</div>';
    $output .= '</div>';

    if ($hasChildren) {
      $output .= displayCategoryTree($categories, $category['id'], $level + 1);
    }

    $output .= '</li>';
  }
  $output .= '</ul>';
  return $output;
}

function EdytujKategorie($link)
{
  if (!isset($_GET['category_id'])) {
    return '<div class="alert alert-error">Nie wybrano kategorii do edycji</div>';
  }

  $id = filter_var($_GET['category_id'], FILTER_VALIDATE_INT);

  if ($id === false) {
    return '<div class="alert alert-error">Nieprawidłowe ID kategorii</div>';
  }

  $query = "SELECT * FROM category WHERE id = ? LIMIT 1";
  $stmt = mysqli_prepare($link, $query);
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $category = mysqli_fetch_assoc($result);

  if (!$category) {
    return '<div class="alert alert-error">Nie znaleziono kategorii o podanym ID</div>';
  }

  $form = '
    <div class="card">
        <h2>Edycja Kategorii</h2>
        <form method="POST">
            <div class="form-group">
                <label for="category_name">Nazwa kategorii:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" 
                       value="' . htmlspecialchars($category['nazwa']) . '" required>
            </div>
            
            <div class="form-group">
                <label for="parent_category">Kategoria nadrzędna:</label>
                <select class="form-control" id="parent_category" name="parent_category">
                    ' . getCategoryOptions($link, $category['matka'], $category['id']) . '
                </select>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="save_category" class="btn btn-success">Zapisz zmiany</button>
                <a href="admin.php?action=categories" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>';

  return $form;
}

function DodajNowaKategorie($link)
{
  $form = '
    <div class="card">
        <h2>Dodaj Nową Kategorię</h2>
        <form method="POST">
            <div class="form-group">
                <label for="category_name">Nazwa kategorii:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <br>
            
            <div class="form-group">
                <label for="parent_category">Kategoria nadrzędna:</label>
                <select class="form-control" id="parent_category" name="matka"
                    ' . getCategoryOptions($link) . '
                </select>
            </div>
            <br>
            
            <div class="form-buttons">
                <button type="submit" name="add_category" class="btn btn-success">Dodaj kategorię</button>
                <a href="admin.php?action=categories" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>';

  return $form;
}

function getCategoryOptions($link, $selectedId = null, $excludeId = null, $isRequired = false)
{
  $query = "SELECT * FROM category ORDER BY nazwa ASC";
  $result = mysqli_query($link, $query);
  $options = '';

  if (!$isRequired) {
    $options .= '<option value="">-- Kategoria nadrzędna (opcjonalnie) --</option>';
  }

  while ($row = mysqli_fetch_assoc($result)) {
    if ($excludeId !== null && $row['id'] == $excludeId) {
      continue;
    }

    $selected = ($selectedId !== null && $row['id'] == $selectedId) ? 'selected' : '';
    $options .= '<option value="' . htmlspecialchars($row['id']) . '" ' . $selected . '>';
    $options .= htmlspecialchars($row['nazwa']);
    $options .= '</option>';
  }
  return $options;
}
