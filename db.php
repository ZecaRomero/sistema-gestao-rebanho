<?php
$host = 'localhost';
$dbname = 'gestao_rebanho'; // O nome do banco que você acabou de criar
$user = 'root'; // Usuário padrão do XAMPP
$pass = ''; // Senha padrão do XAMPP é vazia

try {
    // Usando PDO para uma conexão mais segura
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Em caso de falha na conexão, exibe uma mensagem de erro e encerra o script
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?> 