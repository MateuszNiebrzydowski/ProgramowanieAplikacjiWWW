<?php

function handlePageSubmissions($link)
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_page']) && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false) {
      die("Invalid page ID");
    }

    $title = mysqli_real_escape_string($link, trim($_POST['page_title']));
    $content = mysqli_real_escape_string($link, trim($_POST['page_content']));
    $status = isset($_POST['page_active']) ? 1 : 0;

    $query = "UPDATE page_list SET 
                 page_title = '$title', 
                 page_content = '$content', 
                 status = $status 
                 WHERE id = $id LIMIT 1";

   mysqli_query($link, $query);
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_page'])) {
    $title = mysqli_real_escape_string($link, trim($_POST['page_title']));
    $content = mysqli_real_escape_string($link, trim($_POST['page_content']));
    $status = isset($_POST['page_active']) ? 1 : 0;

    $query = "INSERT INTO page_list (page_title, page_content, status) 
                 VALUES ('$title', '$content', $status)";

    mysqli_query($link, $query);
  }

  if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false) {
      die("Invalid page ID");
    }

    $query = "DELETE FROM page_list WHERE id = $id LIMIT 1";
    mysqli_query($link, $query);
  }
}


function ListaPodstron($link)
{
  $result = '<div class="card">';
  $result .= '<h2>Lista Podstron</h2>';
  $result .= '<a href="?action=add" class="btn btn-success">Dodaj nową stronę</a>';
  $result .= '<table class="table">
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>';

  $query = "SELECT * FROM page_list ORDER BY id ASC LIMIT 100";
  $pages = mysqli_query($link, $query);

  while ($row = mysqli_fetch_array($pages)) {
    $result .= '<tr>
                    <td>' . htmlspecialchars($row['id']) . '</td>
                    <td>' . htmlspecialchars($row['page_title']) . '</td>
                    <td>' . ($row['status'] ? 'Aktywna' : 'Nieaktywna') . '</td>
                    <td>
                        <a href="?action=edit&id=' . htmlspecialchars($row['id']) . '" class="btn btn-primary">Edytuj</a>
                        <a href="?action=delete&id=' . htmlspecialchars($row['id']) . '" 
                           class="btn btn-danger"
                           onclick="return confirm(\'Czy na pewno chcesz usunąć?\')">Usuń</a>
                    </td>
                </tr>';
  }

  $result .= '</table></div>';
  return $result;
}


function EdytujPodstrone($link)
{
  if (!isset($_GET['id'])) {
    return '<div class="alert alert-error">Nie wybrano strony do edycji</div>';
  }

  $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

  if ($id === false) {
    return '<div class="alert alert-error">Nieprawidłowe ID strony</div>';
  }

  $query = "SELECT * FROM page_list WHERE id = ? LIMIT 1";
  $stmt = mysqli_prepare($link, $query);
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $page = mysqli_fetch_assoc($result);

  if (!$page) {
    return '<div class="alert alert-error">Nie znaleziono strony o podanym ID</div>';
  }

  $form = '
    <div class="card">
        <h2>Edycja Podstrony</h2>
        <form method="POST">
            <div class="form-group">
                <label for="page_title">Tytuł strony:</label>
                <input type="text" class="form-control" id="page_title" name="page_title" 
                       value="' . htmlspecialchars($page['page_title']) . '" required>
            </div>
            
            <div class="form-group">
                <label for="page_content">Treść strony:</label>
                <textarea class="form-control" id="page_content" name="page_content" 
                          rows="10" required>' . htmlspecialchars($page['page_content']) . '</textarea>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="page_active" ' . ($page['status'] ? 'checked' : '') . '>
                    Strona aktywna
                </label>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="save_page" class="btn btn-success">Zapisz zmiany</button>
                <a href="admin.php" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>';

  return $form;
}

function DodajNowaPodstrone($link)
{
  $form = '
    <div class="card">
        <h2>Dodaj Nową Podstronę</h2>
        <form method="POST">
            <div class="form-group">
                <label for="page_title">Tytuł strony:</label>
                <input type="text" class="form-control" id="page_title" name="page_title" required>
            </div>
            
            <div class="form-group">
                <label for="page_content">Treść strony:</label>
                <textarea class="form-control" id="page_content" name="page_content" rows="10" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="page_active" checked>
                    Strona aktywna
                </label>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="add_page" class="btn btn-success">Dodaj stronę</button>
                <a href="admin.php" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>';

  return $form;
}
