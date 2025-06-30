<?php
session_start();
require 'db.php';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = 'ID inválido.';
    header('Location: dashboard.php');
    exit;
}
$id = (int)$_GET['id'];

// Busca os dados do animal
$stmt = $pdo->prepare('SELECT * FROM animais WHERE id = :id');
$stmt->execute([':id' => $id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    $_SESSION['erro'] = 'Animal não encontrado.';
    header('Location: dashboard.php');
    exit;
}

function decimal_to_brl($decimal) {
    if ($decimal === null) return '';
    return number_format($decimal, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header-container">
        <img src="logo.png" alt="Logotipo" style="height:48px; margin-right:16px; vertical-align:middle;">
        <h1 style="display:inline-block; vertical-align:middle;">🐮 Editar Animal</h1>
        <a href="dashboard.php" class="btn-voltar">Voltar</a>
    </div>
    <div class="container">
        <form action="atualizar_animal.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">
            <fieldset class="resumo-fieldset">
                <legend>Resumo do Animal</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Série:</label> <input type="text" name="serie" value="<?php echo htmlspecialchars($animal['serie']); ?>" required>
                    </div>
                    <div class="col-flex">
                        <label>RG:</label> <input type="text" name="rg" value="<?php echo htmlspecialchars($animal['rg']); ?>" required>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex pequeno">
                        <label>Raça:</label>
                        <select name="raca" class="pequeno" required>
                            <option value="">Selecione</option>
                            <?php $racas = ["Nelore", "Brahman", "Gir", "Receptoras", "Cruza", "Nelore PA"]; ?>
                            <?php foreach($racas as $raca): ?>
                                <option value="<?php echo $raca; ?>" <?php echo ($animal['raca'] == $raca) ? 'selected' : ''; ?>><?php echo $raca; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-flex" style="display: flex; flex-direction: column; align-items: flex-end;">
                        <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                            <label style="margin-bottom: 6px; text-align: center;">Sexo:</label>
                            <div id="sexo-buttons" class="sexo-buttons-central">
                                <div class="sexo-btns-row">
                                    <button type="button" class="sexo-btn sexo-macho <?php echo ($animal['sexo'] == 'M') ? 'selected' : ''; ?>" data-value="M">Macho</button>
                                    <button type="button" class="sexo-btn sexo-femea <?php echo ($animal['sexo'] == 'F') ? 'selected' : ''; ?>" data-value="F">Fêmea</button>
                                </div>
                                <input type="hidden" name="sexo" id="sexo-input" value="<?php echo htmlspecialchars($animal['sexo']); ?>">
                                <span id="sexo-feedback" class="feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Status:</label>
                        <input type="text" id="status-animal" value="<?php echo ($animal['ativo'] == 'S') ? 'ATIVO' : 'INATIVO'; ?>" readonly style="font-weight:bold; color:#28a745; width: 120px;">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor Adquirido:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_adquirido" id="valor-adquirido" value="<?php echo decimal_to_brl($animal['valor_adquirido']); ?>">
                    </div>
                    <div class="col-flex">
                        <label>Origem da Compra:</label> <input type="text" name="origem_compra" id="origem-compra" value="<?php echo htmlspecialchars($animal['origem_compra']); ?>">
                    </div>
                </div>
            </fieldset>

            <div class="fieldset-row">
                <fieldset class="entrada-fieldset">
                    <legend>Entrada</legend>
                    <label>Origem:</label> <input type="text" name="entrada_origem" id="entrada-origem" value="<?php echo htmlspecialchars($animal['entrada_origem']); ?>"><br>
                    <label>Data:</label> <input type="date" name="entrada_data" value="<?php echo htmlspecialchars($animal['entrada_data']); ?>"><br>
                    <label>Nº N.F:</label> <input type="text" name="entrada_nf" value="<?php echo htmlspecialchars($animal['entrada_nf']); ?>"><br>
                    <label>Natureza:</label> <input type="text" name="entrada_natureza" value="<?php echo htmlspecialchars($animal['entrada_natureza']); ?>"><br>
                    <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="entrada_valor" id="entrada-valor" inputmode="decimal" value="<?php echo decimal_to_brl($animal['entrada_valor']); ?>"><br>
                    <label>Observações:</label> <input type="text" name="entrada_obs" value="<?php echo htmlspecialchars($animal['entrada_obs']); ?>"><br>
                </fieldset>

                <fieldset class="saida-fieldset">
                    <legend>Saída</legend>
                    <label>Destino:</label> <input type="text" name="saida_destino" id="saida-destino" value="<?php echo htmlspecialchars($animal['saida_destino']); ?>"><br>
                    <label>Data:</label> <input type="date" name="saida_data" value="<?php echo htmlspecialchars($animal['saida_data']); ?>"><br>
                    <label>Nº N.F:</label> <input type="text" name="saida_nf" value="<?php echo htmlspecialchars($animal['saida_nf']); ?>"><br>
                    <label>Natureza:</label> <input type="text" name="saida_natureza" value="<?php echo htmlspecialchars($animal['saida_natureza']); ?>"><br>
                    <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="saida_valor" id="saida-valor" inputmode="decimal" value="<?php echo decimal_to_brl($animal['saida_valor']); ?>"><br>
                    <label>Observações:</label> <input type="text" name="saida_obs" value="<?php echo htmlspecialchars($animal['saida_obs']); ?>"><br>
                </fieldset>
            </div>

            <fieldset class="situacao-fieldset">
                <legend>Situação do Animal</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Prenha:</label>
                        <select name="prenha">
                            <option value="">Selecione</option>
                            <option value="S" <?php if($animal['prenha']==='S') echo 'selected'; ?>>Sim</option>
                            <option value="N" <?php if($animal['prenha']==='N') echo 'selected'; ?>>Não</option>
                        </select>
                    </div>
                    <div class="col-flex">
                        <label>Previsão de Parto:</label> <input type="date" name="previsao_parto" value="<?php echo htmlspecialchars($animal['previsao_parto']); ?>">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Data do Parto:</label> <input type="date" name="data_parto" value="<?php echo htmlspecialchars($animal['data_parto']); ?>">
                    </div>
                    <div class="col-flex">
                        <label>Sexo da cria:</label>
                        <select name="sexo_bezerro">
                            <option value="">Selecione</option>
                            <option value="M" <?php if($animal['sexo_bezerro']==='M') echo 'selected'; ?>>Macho</option>
                            <option value="F" <?php if($animal['sexo_bezerro']==='F') echo 'selected'; ?>>Fêmea</option>
                        </select>
                    </div>
                </div>
                <label>Tipo de Cobertura:</label>
                <select name="cobertura">
                    <option value="">Selecione</option>
                    <option value="TE" <?php if($animal['cobertura']==='TE') echo 'selected'; ?>>IA</option>
                    <option value="FIV" <?php if($animal['cobertura']==='FIV') echo 'selected'; ?>>FIV</option>
                </select><br>
            </fieldset>

            <input type="submit" value="Atualizar Dados">
        </form>
    </div>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html> 