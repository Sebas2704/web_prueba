<?php
// Habilitar la visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mensajes de éxito o error
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['confirm-password']) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Conexión a la base de datos
        $servername = "localhost";  // Servidor de la base de datos
        $db_username = "root";     // Usuario de MySQL
        $db_password = "";         // Contraseña de MySQL
        $dbname = "usuario";       // Nombre de la base de datos

        // Crear conexión
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Error en la conexión a la base de datos: " . $conn->connect_error);
        }

        // Recibir y sanitizar datos del formulario
        $nombre_completo = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $usuario = htmlspecialchars($_POST['username']);
        $contraseña = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña

        // Insertar datos en la base de datos usando consultas preparadas
        $stmt = $conn->prepare("INSERT INTO registros (nombre_completo, email, usuario, contraseña) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("ssss", $nombre_completo, $email, $usuario, $contraseña);

        // Ejecutar la consulta y verificar si se insertaron los datos
        if ($stmt->execute()) {
            $mensaje = "Registro exitoso. ¡Puedes iniciar sesión ahora!";
            // Redirigir a login.php después del registro exitoso
            header("Location: login.php");
            exit(); // Asegurarse de que no se ejecute más código después de la redirección
        } else {
            $mensaje = "Error al registrar el usuario. Por favor, intenta nuevamente.";
        }

        // Cerrar la conexión y la consulta preparada
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="estilo.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
</head>
<body>
    <div class="register-container">
        <h2 class="animate__animated animate__rubberBand">Registro</h2>

        <!-- Mostrar mensaje de error o éxito -->
        <?php if ($mensaje): ?>
            <div class="message"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="input-group">
                <label for="name">Nombre completo</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm-password">Confirmar contraseña</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
