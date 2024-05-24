<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "aguadas";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("No hay conexión: " . mysqli_connect_error());
}
$nombre = mysqli_real_escape_string($conn, $_POST["name"]);
$query = mysqli_query($conn, "SELECT * FROM login WHERE usuario = '" . $nombre . "'");
echo mysqli_num_rows($query);
