<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "moja_strona";

$login = "admin";
$password = "ZAQ!2wsx";

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$link) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

