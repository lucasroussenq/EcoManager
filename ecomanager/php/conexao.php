<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ecomanager";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
