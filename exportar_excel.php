<?php
session_start();
require 'db.php'; // Incluindo a conexão

if (empty($_SESSION['logado'])) {
    header('Location: index.html');
    exit;
}

// Nome do arquivo para download
$filename = "rebanho_" . date('Y-m-d') . ".xls";

// Define os cabeçalhos para forçar o download
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Busca todos os dados da tabela
$stmt = $pdo->query("SELECT * FROM animais ORDER BY id ASC");
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = fopen("php://output", "w");

// Se não houver animais, encerra a execução.
if (empty($animais)) {
    fclose($output);
    exit;
}

// Lógica para encontrar colunas que não estão completamente vazias
$nonEmptyColumns = [];
$allColumns = array_keys($animais[0]);

foreach ($allColumns as $column) {
    foreach ($animais as $animal) {
        // Se a coluna tiver algum valor (não nulo e não vazio) em qualquer linha, a adicionamos e passamos para a próxima.
        if ($animal[$column] !== null && $animal[$column] !== '') {
            $nonEmptyColumns[] = $column;
            break; 
        }
    }
}

// Escreve o cabeçalho da planilha apenas com as colunas que têm dados
if (!empty($nonEmptyColumns)) {
    fputcsv($output, $nonEmptyColumns, "\t");
}

// Escreve os dados de cada animal, mas apenas das colunas que têm dados
foreach ($animais as $animal) {
    $filteredRow = [];
    foreach ($nonEmptyColumns as $column) {
        $filteredRow[] = $animal[$column];
    }
    fputcsv($output, $filteredRow, "\t");
}

fclose($output);
exit;
?> 