<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$instrumento = $_POST['instrumento'];

$sql = "INSERT INTO usuarios (nombre, telefono, email, password, instrumento)
        VALUES ('$nombre', '$telefono', '$email', '$password', '$instrumento')";

if ($conn->query($sql) === TRUE) {
    echo "Usuario registrado correctamente";
} else {
    echo "Error: " . $conn->error;
}
?>