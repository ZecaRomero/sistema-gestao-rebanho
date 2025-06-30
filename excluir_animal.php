<?php
session_start();
require 'db.php'; // Incluindo a conexão

// Verifica se o usuário está logado e se o ID foi passado
if (empty($_SESSION['logado']) || empty($_GET['id'])) {
    header('Location: index.html');
    exit;
}

$id = $_GET['id'];

try {
    // Prepara e executa a query de exclusão
    $stmt = $pdo->prepare("DELETE FROM animais WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Define uma mensagem de sucesso
    $_SESSION['mensagem'] = "Animal (ID: $id) excluído com sucesso!";

} catch (PDOException $e) {
    // Em caso de erro, define uma mensagem de erro
    $_SESSION['erro'] = "Erro ao excluir o animal: " . $e->getMessage();
}

// Redireciona de volta para a lista de animais
header('Location: lista_animais.php');
exit;
?> 