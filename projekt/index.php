<?php

include_once './cfg.php';
include_once './showpage.php';

$page = $_GET['page'] ?? 'index';

list($title, $content) = showPage($page);
$pageList = showPageList($link);
?>


<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <meta http-equiv="Content-Language" content="pl">
        <meta name="Author" content="Mateusz Niebrzydowski">
        <title>Hodowla żółwia wodnego</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link rel="icon" type="image/x-icon" href="images\favicon.png">
    </head>
    <body>
        <div class="container">
            <header>
                <h1>Hodowla żółwi wodnych</h1>
            </header>
            <nav>
                <ul>
                    <li><a href="index.php">Strona główna</a></li>
                    <?php
                    while ($row = mysqli_fetch_array($pageList)) {
                    echo '<li><a href="index.php?page='.htmlspecialchars($row['page_title']).'">'
                    .htmlspecialchars($row['page_title']).'</a></li>';
                    }
                    ?>
                    <li><a href="admin.php">Panel administratora</a></li>
                </ul>
            </nav>
            <section class="center">
                <h2>Witamy na stronie o hodowli żółwi wodnych</h2>
                <br>
                <p><b>Żółwie wodne</b> to fascynujące zwierzęta, które mogą stać się niezwykłymi towarzyszami. Odpowiednia pielęgnacja zapewni im długie i zdrowe życie.</p>
                <p>Żółwie wodne spędzają większość swojego życia w wodzie, jednak niektóre gatunki potrzebują także lądowej przestrzeni.</p>
                <br>
                <p>Przejdź na inne strony, aby dowiedzieć się więcej.</p>
                <br>
                <img src="images/turtles.jpg" alt="żółwie">
            </section>
            <footer>
                <?php
                $nr_indeksu = '169342';
                $nrGrupy = '3';
                echo 'Autor: Mateusz Niebrzydowski <br>';
                echo 'Nr indeksu: '.htmlspecialchars($nr_indeksu).'<br>';
                echo 'Grupa: '.htmlspecialchars($nrGrupy) .'<br>';
                ?>
            </footer>
        </div>
    </body>
</html>