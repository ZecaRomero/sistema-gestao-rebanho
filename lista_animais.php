<?php
session_start();
require 'db.php';

// Buscar todos os animais
$animais = $pdo->query("SELECT * FROM animais ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Mensagens de sessão
$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Animais</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header-container">
        <h1>🐮 Lista de Animais</h1>
        <a href="dashboard.php" class="btn-voltar">Voltar</a>
    </div>
    <?php if ($mensagem): ?>
        <div class="notificacao sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="notificacao erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>
    <div class="container">
        <h2>Animais Cadastrados</h2>
        <a href="exportar_excel.php" class="btn-exportar">Exportar para Excel</a>
        <div class="table-container">
            <table class="animal-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Série</th>
                        <th>RG</th>
                        <th>Raça</th>
                        <th>Sexo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($animais as $animal): ?>
                        <tr>
                            <td><?php echo $animal['id']; ?></td>
                            <td><?php echo htmlspecialchars($animal['serie']); ?></td>
                            <td><?php echo htmlspecialchars($animal['rg']); ?></td>
                            <td><?php echo htmlspecialchars($animal['raca']); ?></td>
                            <td><?php echo $animal['sexo'] === 'M' ? 'Macho' : ($animal['sexo'] === 'F' ? 'Fêmea' : ''); ?></td>
                            <td><?php echo $animal['ativo'] === 'S' ? 'Ativo' : 'Inativo'; ?></td>
                            <td class="actions">
                                <a href="editar_animal.php?id=<?php echo $animal['id']; ?>">Editar</a>
                                <a href="excluir_animal.php?id=<?php echo $animal['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este animal?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 