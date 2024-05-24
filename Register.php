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

$nombre = mysqli_real_escape_string($conn, $_POST["name"]);
$query = mysqli_query($conn, "SELECT * FROM login WHERE usuario = '" . $nombre . "'");
$nr = mysqli_num_rows($query);
if ($nr == 1) {
    // Redirige a registro.html con el parámetro de error "username_exists"
    header("Location: Register.html?error=username_exists");
    exit();
}
$email = mysqli_real_escape_string($conn, $_POST["email"]);
$pass = mysqli_real_escape_string($conn, $_POST["password"]);
$confirmar_contrasena = mysqli_real_escape_string($conn, $_POST["confirm-password"]);


if ($pass !== $confirmar_contrasena) {
    echo "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
} else {

    $stmt = $conn->prepare("INSERT INTO login (usuario, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $pass, $email);

    if ($stmt->execute()) {
        // La inserción fue exitosa.
        $_SESSION["username"] = $nombre;
        
        header("Location: index.php");
    } else {
        // Error en la inserción.
        // header("Location: index.php");
        echo "No ingreso";
    }
    $stmt->close();

}

mysqli_close($conn);