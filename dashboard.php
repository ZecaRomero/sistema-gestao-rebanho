<?php
session_start();
require 'db.php'; // Incluindo a conexão com o banco

if (empty($_SESSION['logado'])) {
    header('Location: index.html');
    exit;
}

// Buscar estatísticas
$total_animais = $pdo->query("SELECT COUNT(*) FROM animais")->fetchColumn();
$ultimo_animal = $pdo->query("SELECT serie, rg FROM animais ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

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
    <title>Cadastro de Animal</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header-container">
        <img src="logo.png" alt="Logotipo" style="height:48px; margin-right:16px; vertical-align:middle;">
        <h1 style="display:inline-block; vertical-align:middle;">🐮 Sistema de Gestão de Rebanho</h1>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <!-- Container de Notificações -->
    <?php if ($mensagem): ?>
        <div class="notificacao sucesso"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="notificacao erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <!-- Container de Estatísticas -->
    <div class="stats-container">
        <a href="lista_animais.php" class="stat-card-link">
            <div class="stat-card">
                <h2>Total de Animais</h2>
                <p><?php echo $total_animais; ?></p>
            </div>
        </a>
        <div class="stat-card">
            <h2>Último Animal Cadastrado</h2>
            <?php if ($ultimo_animal): ?>
                <p>Série: <?php echo htmlspecialchars($ultimo_animal['serie']); ?> / RG: <?php echo htmlspecialchars($ultimo_animal['rg']); ?></p>
            <?php else: ?>
                <p>Nenhum animal cadastrado</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <form action="salvar.php" method="POST">
            <fieldset class="resumo-fieldset">
                <legend>Resumo do Animal</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Série:</label> <input type="text" name="serie" value="" required>
                    </div>
                    <div class="col-flex">
                        <label>RG:</label> <input type="text" name="rg" value="" required>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex pequeno">
                        <label>Raça:</label>
                        <select name="raca" class="pequeno" required>
                            <option value="">Selecione</option>
                            <option value="Nelore">Nelore</option>
                            <option value="Brahman">Brahman</option>
                            <option value="Gir">Gir</option>
                            <option value="Receptoras">Receptoras</option>
                            <option value="Cruza">Cruza</option>
                            <option value="Nelore PA">Nelore PA</option>
                        </select>
                    </div>
                    <div class="col-flex" style="display: flex; flex-direction: column; align-items: flex-end;">
                        <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                            <label style="margin-bottom: 6px; text-align: center;">Sexo:</label>
                            <div id="sexo-buttons" class="sexo-buttons-central">
                                <div class="sexo-btns-row">
                                    <button type="button" class="sexo-btn sexo-macho" data-value="M" aria-label="Selecionar macho">Macho</button>
                                    <button type="button" class="sexo-btn sexo-femea" data-value="F" aria-label="Selecionar fêmea">Fêmea</button>
                                </div>
                                <input type="hidden" name="sexo" id="sexo-input">
                                <span id="sexo-feedback" class="feedback"></span>
                            </div>
                        </div>
                        <span id="valor-real-feedback" class="feedback"></span>
                        <div id="grafico-valor-real" style="width:100%;margin-top:8px;"></div>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Status:</label>
                        <input type="text" id="status-animal" value="ATIVO" readonly style="font-weight:bold; color:#28a745; width: 120px;">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor Adquirido:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_adquirido" id="valor-adquirido" readonly>
                    </div>
                    <div class="col-flex">
                        <label>Origem da Compra:</label> <input type="text" name="origem_compra" id="origem-compra" readonly>
                    </div>
                </div>
            </fieldset>

            <div class="fieldset-row">
                <fieldset class="entrada-fieldset">
                    <legend>Entrada</legend>
                    <label>Origem:</label> <input type="text" name="entrada_destino" id="entrada-destino"><br>
                    <label>Data:</label> <input type="date" name="entrada_data"><br>
                    <label>Nº N.F:</label> <input type="text" name="entrada_nf"><br>
                    <label>Natureza:</label> <input type="text" name="entrada_natureza"><br>
                    <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="entrada_valor" id="entrada-valor" inputmode="decimal"><br>
                    <label>Observações:</label> <input type="text" name="entrada_obs"><br>
                </fieldset>

                <fieldset class="saida-fieldset">
                    <legend>Saída</legend>
                    <label>Destino:</label> <input type="text" 
                    <label>Data:</label> <input type="date" name="saida_data"><br>
                    <label>Nº N.F:</label> <input type="text" name="saida_nf"><br>
                    <label>Natureza:</label> <input type="text" name="saida_natureza"><br>
                    <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="saida_valor" id="saida-valor" inputmode="decimal"><br>
                    <label>Observações:</label> <input type="text" name="saida_obs"><br>
                </fieldset>
            </div>

            <fieldset class="situacao-fieldset">
                <legend>Situação do Animal</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Prenha:</label>
                        <select name="prenha">
                            <option value="">Selecione</option>
                            <option value="S">Sim</option>
                            <option value="N">Não</option>
                        </select>
                    </div>
                    <div class="col-flex">
                        <label>Previsão de Parto:</label> <input type="date" name="previsao_parto">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Data do Parto:</label> <input type="date" name="data_parto">
                    </div>
                    <div class="col-flex">
                        <label>Sexo da cria:</label>
                        <select name="sexo_bezerro">
                            <option value="">Selecione</option>
                            <option value="M">Macho</option>
                            <option value="F">Fêmea</option>
                        </select>
                    </div>
                </div>
                <label>Tipo de Cobertura:</label>
                <select name="cobertura">
                    <option value="">Selecione</option>
                    <option value="TE">IA</option>
                    <option value="FIV">FIV</option>
                </select><br>
            </fieldset>

            <input type="submit" value="Salvar Dados">
        </form>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html> 