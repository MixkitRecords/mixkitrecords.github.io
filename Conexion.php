<?php
$conn = new mysqli("sqlXXX.infinityfree.com", "if0_41445356", "2011", "if0_41445356_mixkit");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>