<?php
session_start();
header('Content-Type: application/json'); // Adicionei isso

$usuario = $_POST['username'] ?? '';
$senha = $_POST['password'] ?? '';

if ($usuario === 'admin' && $senha === '1234') {
    $_SESSION['logado'] = true;
    echo json_encode(['success' => true, 'redirect' => 'dashboard.php']); // Adicionei o redirect
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos.']);
}
?>