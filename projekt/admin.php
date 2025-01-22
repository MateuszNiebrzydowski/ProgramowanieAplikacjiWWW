<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once('cfg.php');
require_once('admin-kategorie.php');
require_once('admin_strony.php');

function Logout()
{
  session_destroy();
  header("Location: admin.php");
  exit();
}

function AdminHeader()
{
  $current_page = $_GET['action'] ?? '';
  $pages_active = empty($current_page) || in_array($current_page, ['add', 'edit', 'delete']) ? 'active' : '';
  $categories_active = strpos($current_page, 'category') !== false || $current_page === 'categories' ? 'active' : '';
  $products_active = strpos($current_page, 'product') !== false || $current_page === 'products' ? 'active' : '';

  return '
    <div class="admin-header">
        <h1>Panel Administratora</h1>
        <div class="admin-nav">
            <a href="admin.php" class="nav-link ' . $pages_active . '">Strony</a>
            <a href="?action=categories" class="nav-link ' . $categories_active . '">Kategorie</a>
            <a href="?action=products" class="nav-link ' . $products_active . '">Produkty</a>
            <a href="?action=logout" class="logout-button">Wyloguj się</a>
        </div>
    </div>';
}

function LoginForm()
{
  $result = '
    <div>
        <h1>Panel Administratora</h1>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="POST" name="LoginForm" class="login-form-content">
            <div class="form-group">
                <p>Login: <input type="login" name="login_email"></p>
            </div>
            
            <div class="form-group">
                <p>Hasło: <input type="password" name="login_password"></p>
            </div>
            <br>
            
            <button type="submit">Zaloguj się</button>
        </form>
    </div>';

  return $result;
}

function LoginCheck()
{
  include('cfg.php');

  if (isset($_POST['login_email']) && isset($_POST['login_password'])) {
    if ($_POST['login_email'] == $login && $_POST['login_password'] == $password) {
      $_SESSION['logged_in'] = true;
      return true;
    } else {
      return "Nieprawidłowy email lub hasło";
    }
  }

  return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}


?>
<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administratora</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/admin.css">
</head>

<body>
  <div class="content-container">
    <?php
    $login_status = LoginCheck();

    if ($login_status === true) {
      if (strpos($_GET['action'] ?? '', 'category') !== false) {
        handleCategorySubmissions($link);
      } else {
        handlePageSubmissions($link);
      }

      echo AdminHeader();

      $action = $_GET['action'] ?? '';

      echo '<div class="card">';
      if ($action === "logout") {
        Logout();
      } else if ($action === 'categories') {
        echo ListaKategorii($link);
      } else if ($action === 'add_category') {
        echo DodajNowaKategorie($link);
      } else if ($action === 'edit_category') {
        echo EdytujKategorie($link);
      } else if ($action === 'add') {
        echo DodajNowaPodstrone($link);
      } else if ($action === 'edit') {
        echo EdytujPodstrone($link);
      } else {
        echo ListaPodstron($link);
      }
      echo '</div>';
    } else {
      echo '<div class="login-container">';
      echo '<div class="login-wrapper">';
      if ($login_status !== false) {
        echo '<div class="login-error">
                <span class="error-icon">⚠️</span>
                <span class="error-message">' . $login_status . '</span>
              </div>';
      }
      echo LoginForm();
      echo '</div></div>';
    }
    ?>
  </div>
</body>

</html>
