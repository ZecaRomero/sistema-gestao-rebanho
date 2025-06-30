<?php
session_start();
require 'db.php'; // Incluindo a conexão

if (empty($_SESSION['logado'])) {
    header('Location: index.html');
    exit;
}

// Buscar todos os animais
$stmt = $pdo->query("SELECT id, serie, rg, raca, sexo, ativo FROM animais ORDER BY id DESC");
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pega e limpa as mensagens da sessão
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
    <title>Lista de Animais - Gestão de Rebanho</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header-container">
        <h1>🐮 Lista de Animais Cadastrados</h1>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <!-- Container de Notificações -->
    <?php if ($mensagem): ?>
        <div class="notificacao sucesso" style="max-width: 90%; margin-left: auto; margin-right: auto;"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="notificacao erro" style="max-width: 90%; margin-left: auto; margin-right: auto;"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <div class="table-container">
        <div class="table-actions">
            <a href="dashboard.php" class="btn-voltar">Voltar ao Dashboard</a>
            <a href="exportar_excel.php" class="btn-exportar">Exportar para Excel</a>
        </div>
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
                <?php if (count($animais) > 0): ?>
                    <?php foreach ($animais as $animal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($animal['id']); ?></td>
                            <td><?php echo htmlspecialchars($animal['serie']); ?></td>
                            <td><?php echo htmlspecialchars($animal['rg']); ?></td>
                            <td><?php echo htmlspecialchars($animal['raca']); ?></td>
                            <td>
                                <?php 
                                    if ($animal['sexo'] === 'M') echo 'Macho';
                                    elseif ($animal['sexo'] === 'F') echo 'Fêmea';
                                    else echo 'N/D';
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if ($animal['ativo'] === 'S') echo '<span style="color: green; font-weight: bold;">Ativo</span>';
                                    else echo '<span style="color: red; font-weight: bold;">Inativo</span>';
                                ?>
                            </td>
                            <td class="actions">
                                <a href="editar_animal.php?id=<?php echo $animal['id']; ?>">Editar</a>
                                <a href="excluir_animal.php?id=<?php echo $animal['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este animal?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Nenhum animal encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html> 