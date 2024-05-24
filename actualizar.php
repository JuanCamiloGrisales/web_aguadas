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

$nombre = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_user"])) {
        $nombre = mysqli_real_escape_string($conn, $_POST["name"]);
        $email = mysqli_real_escape_string($conn, $_POST["email"]);

        // Verificar si el usuario ya existe, excepto si es el mismo usuario
        if ($nombre != $_SESSION["username"]) {
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM login WHERE usuario = ?");
            $stmt_check->bind_param("s", $nombre);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($count > 0) {
                // El usuario ya existe, mostrar el toast
                $update_message = "El usuario ingresado ya existe";
            } else {
                // El usuario no existe o es el mismo, actualizar
                $stmt = $conn->prepare("UPDATE login SET usuario = ?, email = ? WHERE usuario = ?");
                $stmt->bind_param("sss", $nombre, $email, $_SESSION["username"]);
                if ($stmt->execute()) {
                    // La actualización fue exitosa.
                    $_SESSION["username"] = $nombre;
                    $update_message = "Actualización exitosa";
                } else {
                    // Error en la actualización.
                    $update_message = "Error en la actualización";
                }
                $stmt->close();
            }
        } else {
            // El usuario está actualizando solo su email
            $stmt = $conn->prepare("UPDATE login SET email = ? WHERE usuario = ?");
            $stmt->bind_param("ss", $email, $_SESSION["username"]);
            if ($stmt->execute()) {
                // La actualización fue exitosa.
                $update_message = "Actualización exitosa";
            } else {
                // Error en la actualización.
                $update_message = "Error en la actualización";
            }
            $stmt->close();
        }

    } else if (isset($_POST["update_password"])) {
        $pass = mysqli_real_escape_string($conn, $_POST["password"]);
        $confirmar_contrasena = mysqli_real_escape_string($conn, $_POST["confirm-password"]);
        if ($pass !== $confirmar_contrasena) {
            $update_message = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
        } else {
            $stmt = $conn->prepare("UPDATE login SET password = ? WHERE usuario = ?");
            $stmt->bind_param("ss", $pass, $_SESSION["username"]);
            if ($stmt->execute()) {
                // La actualización fue exitosa.
                $update_message = "Actualización exitosa";
            } else {
                // Error en la actualización.
                $update_message = "Error en la actualización";
            }
            $stmt->close();
        }
    }
} else {
    $stmt = $conn->prepare("SELECT usuario, email FROM login WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $stmt->bind_result($nombre, $email);
    $stmt->fetch();
    $stmt->close();
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Actualizar Perfil</title>
    <style>
        /* Importa una fuente moderna */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url("https://caracoltv.brightspotcdn.com/dims4/default/734f124/2147483647/strip/true/crop/806x414+57+0/resize/1440x740!/quality/90/?url=http%3A%2F%2Fcaracol-brightspot.s3.us-west-2.amazonaws.com%2F69%2F16%2Ffa5a7ac343f59b4d4cd9aee1acb3%2Fimagen-1.png");
            background-size: cover;
            /* Gradiente de fondo */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Dos columnas con el mismo ancho */
            grid-gap: 30px;
            justify-content: center;
            /* Centra las columnas horizontalmente */
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }

        h2 {
            color: #008080;
            /* Color de encabezado moderno */
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #008080;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #006666;
        }

        .toast {
            position: fixed;
            top: 10vh;
            right: 0;
            left: 0;
            margin: auto;
            width: 300px;
            text-align: center;
            background-color: #fff;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: 100;
            /* Asegúrate de que esté por encima de otros elementos */
        }

        .toast.show {
            opacity: 1;
        }

        /* Estilos para el botón de regreso */
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #008080;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1.2em; /* Tamaño de fuente grande */
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #006666;
        }

        /* Icono de regreso */
        .back-button i {
            margin-right: 10px; /* Espacio entre el icono y el texto */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <a href="index.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Regresar
    </a>
    <div class="container">
        <div class="card">
            <h2>Actualizar Usuario</h2>
            <form method="post" action="actualizar.php">
                <label for="name">Nuevo nombre de usuario:</label><br>
                <input type="text" id="name" name="name" value="<?php echo $nombre; ?>" required><br>
                <label for="email">Nuevo correo electrónico:</label><br>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required><br>
                <input type="submit" name="update_user" value="Actualizar Usuario y Correo">
            </form>
        </div>

        <div class="card">
            <h2>Actualizar Contraseña</h2>
            <form method="post" action="actualizar.php" onsubmit="return validatePassword()">
                <label for="password">Nueva contraseña:</label><br>
                <input type="password" id="password" name="password"><br>
                <label for="confirm-password">Confirmar nueva contraseña:</label><br>
                <input type="password" id="confirm-password" name="confirm-password"><br>
                <input type="submit" name="update_password" value="Actualizar Contraseña">
            </form>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        function validatePassword() {
            var password = document.getElementById("password");
            var confirm_password = document.getElementById("confirm-password");
            if (password.value != confirm_password.value) {
                alert("Las contraseñas no coinciden.");
                return false;
            }
            return true;
        }

        // Función para mostrar el toast
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 5000); // Oculta el toast después de 5 segundos
        }

        // Mostrar el toast si hay un mensaje de actualización
        <?php if (isset($update_message)): ?>
            showToast("<?php echo $update_message; ?>");
        <?php endif; ?>

    </script>
</body>

</html>