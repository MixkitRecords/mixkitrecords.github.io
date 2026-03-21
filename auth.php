<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/Conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metodo no permitido"]);
    exit;
}

$action = trim($_POST["action"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$password = $_POST["password"] ?? "";
$instrumento = trim($_POST["instrumento"] ?? "");

if ($action !== "login" && $action !== "register") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Accion no valida"]);
    exit;
}

if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Correo y contrasena son obligatorios"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Correo invalido"]);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Contrasena demasiado corta"]);
    exit;
}

if ($action === "register" && ($nombre === "" || $instrumento === "")) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Nombre e instrumento obligatorios para registro"]);
    exit;
}

$checkStmt = $conn->prepare("SELECT id, nombre, email, password, instrumento FROM usuarios WHERE email = ?");
if (!$checkStmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error preparando validacion"]);
    exit;
}

$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($action === "login") {
    if (!$result || $result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }

    $usuario = $result->fetch_assoc();
    if (!password_verify($password, $usuario["password"])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Contrasena incorrecta"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }

    echo json_encode([
        "status" => "login",
        "nombre" => $usuario["nombre"],
        "email" => $usuario["email"],
        "instrumento" => $usuario["instrumento"]
    ]);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

if ($result && $result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["status" => "error", "message" => "Este correo ya esta registrado"]);
    $conn->close();
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$insertStmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, instrumento) VALUES (?, ?, ?, ?)");
if (!$insertStmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error preparando registro"]);
    $conn->close();
    exit;
}

$insertStmt->bind_param("ssss", $nombre, $email, $passwordHash, $instrumento);

if ($insertStmt->execute()) {
    echo json_encode([
        "status" => "registro",
        "nombre" => $nombre,
        "email" => $email,
        "instrumento" => $instrumento
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "No se pudo registrar"]);
}

$insertStmt->close();
$conn->close();
?>