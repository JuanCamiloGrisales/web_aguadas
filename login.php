<?php
session_start();

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "aguadas";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);


if (!$conn) {
    die("No hay conexión: " . mysqli_connect_error());
}

$nombre = $_POST["username"];
$pass = $_POST["password"];
$query = mysqli_query($conn, "SELECT * FROM login WHERE usuario = '" . $nombre . "' and password = '" . $pass . "'");

$nr = mysqli_num_rows($query);
if ($nr == 1) {
    $_SESSION["username"] = $nombre;
    header("Location: index.php");
    exit();
} else {
    // Redirect to login.html with error code 
    header("Location: login.html?error=401");
    exit();
}