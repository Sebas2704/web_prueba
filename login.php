<?php
session_start();

// Habilitar la visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mensaje = '';  // Variable para el mensaje de emergencia

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "usuario";

    // Conexión a la base de datos
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error en la conexión a la base de datos: " . $conn->connect_error);
    }

    // Obtener los datos del formulario
    $usuario = htmlspecialchars($_POST['username']);
    $contraseña = $_POST['password'];

    // Preparar la consulta SQL
    $stmt = $conn->prepare("SELECT id, contraseña FROM registros WHERE usuario = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular los parámetros y ejecutar la consulta
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    // Verificar si los datos coinciden
    if ($stmt->fetch() && password_verify($contraseña, $hashed_password)) {
        // Si el inicio de sesión es exitoso, almacenar el ID del usuario en la sesión
        $_SESSION['user_id'] = $id;
        
        // Redirigir a la página de inicio (inicio.html)
        header("Location: inicio.html");
        exit();
    } else {
        // Si las credenciales son incorrectas, mostrar un mensaje de error
        $mensaje = "Usuario o contraseña incorrectos. Vuelva a intentar.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="estilo.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
</head>
<body>
    <div class="login-container">
        <h2 class="animate__animated animate__zoomIn">Iniciar Sesión</h2>
        
        <!-- Formulario de inicio de sesión -->
        <form action="login.php" method="post">
            <div class="input-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <!-- Mostrar el mensaje de error en caso de que haya uno -->
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
    </div>
</body>
</html>
