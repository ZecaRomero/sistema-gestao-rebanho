<?php
session_start();
require 'db.php'; // Incluindo a conexão

// Verifica se o usuário está logado e se o ID foi passado
if (empty($_SESSION['logado']) || empty($_GET['id'])) {
    header('Location: index.html');
    exit;
}

$id = $_GET['id'];

// Busca os dados do animal específico
$stmt = $pdo->prepare("SELECT * FROM animais WHERE id = :id");
$stmt->execute([':id' => $id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o animal não for encontrado, redireciona com erro
if (!$animal) {
    $_SESSION['erro'] = "Animal não encontrado.";
    header('Location: lista_animais.php');
    exit;
}

// Função para formatar o valor decimal para o formato BRL (1234.56 -> 1.234,56)
function decimal_to_brl($decimal) {
    if ($decimal === null) {
        return '';
    }
    return number_format($decimal, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal - Gestão de Rebanho</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header-container">
        <h1>🐮 Editando Animal: <?php echo htmlspecialchars($animal['serie'] ?: "ID " . $animal['id']); ?></h1>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>

    <div class="container">
        <form action="atualizar_animal.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">

            <div class="fieldset-row">
                <fieldset class="cadastro-fieldset">
                    <legend>Cadastro do Animal</legend>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Série:</label> <input type="text" name="serie" value="<?php echo htmlspecialchars($animal['serie']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>RG:</label> <input type="text" name="rg" value="<?php echo htmlspecialchars($animal['rg']); ?>">
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Nascimento:</label> <input type="date" name="nascimento" value="<?php echo htmlspecialchars($animal['nascimento']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Meses:</label> <input type="number" name="meses" min="0" readonly value="<?php echo htmlspecialchars($animal['meses']); ?>"> <span id="idade-anos" class="idade-anos"></span>
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex pequeno">
                            <label>Raça:</label>
                            <select name="raca" class="pequeno">
                                <?php $racas = ["Nelore", "Brahman", "Gir", "Receptoras", "Cruza", "Nelore PA"]; ?>
                                <option value="">Selecione</option>
                                <?php foreach($racas as $raca): ?>
                                    <option value="<?php echo $raca; ?>" <?php echo ($animal['raca'] == $raca) ? 'selected' : ''; ?>><?php echo $raca; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-flex">
                            <div id="sexo-buttons" class="sexo-buttons-central">
                                <label class="sexo-label-central">Sexo:</label>
                                <div class="sexo-btns-row">
                                    <button type="button" class="sexo-btn sexo-macho <?php echo ($animal['sexo'] == 'M') ? 'selected' : ''; ?>" data-value="M">Macho</button>
                                    <button type="button" class="sexo-btn sexo-femea <?php echo ($animal['sexo'] == 'F') ? 'selected' : ''; ?>" data-value="F">Fêmea</button>
                                </div>
                                <input type="hidden" name="sexo" id="sexo-input" value="<?php echo htmlspecialchars($animal['sexo']); ?>">
                                <span id="sexo-feedback" class="feedback"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Pai:</label> <input type="text" name="pai" value="<?php echo htmlspecialchars($animal['pai']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Avô Materno:</label> <input type="text" name="avo_materno" value="<?php echo htmlspecialchars($animal['avo_materno']); ?>">
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Mãe Biológica:</label> <input type="text" name="mae_biologica" value="<?php echo htmlspecialchars($animal['mae_biologica']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Receptora:</label> <input type="text" name="receptora" value="<?php echo htmlspecialchars($animal['receptora']); ?>">
                        </div>
                    </div>
                    <label>Ativo:</label>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <div id="ativo-buttons">
                            <button type="button" class="ativo-btn ativo-sim <?php echo ($animal['ativo'] == 'S') ? 'selected' : ''; ?>" data-value="S">Sim</button>
                            <button type="button" class="ativo-btn ativo-nao <?php echo ($animal['ativo'] == 'N') ? 'selected' : ''; ?>" data-value="N">Não</button>
                            <input type="hidden" name="ativo" id="ativo-input" value="<?php echo htmlspecialchars($animal['ativo']); ?>">
                        </div>
                        <div id="animal-status-texto" style="margin-left: 20px; font-weight: bold; font-size: 1.5em;"></div>
                    </div>

                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Valor Adquirido:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_adquirido" inputmode="decimal" value="<?php echo decimal_to_brl($animal['valor_adquirido']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Origem da Compra:</label> <input type="text" name="origem_compra" value="<?php echo htmlspecialchars($animal['origem_compra']); ?>">
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Valor Abate:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_abate" inputmode="decimal" value="<?php echo decimal_to_brl($animal['valor_abate']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Destino Abate:</label> <input type="text" name="destino_abate" value="<?php echo htmlspecialchars($animal['destino_abate']); ?>">
                        </div>
                    </div>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Valor Venda:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_venda" inputmode="decimal" value="<?php echo decimal_to_brl($animal['valor_venda']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Destino Venda:</label> <input type="text" name="destino_venda" value="<?php echo htmlspecialchars($animal['destino_venda']); ?>">
                        </div>
                    </div>
                    <div id="inativacao-container" style="display: none; margin-top: 15px;">
                        <label>Data de Inativação:</label> <input type="date" name="data_inativacao" value="<?php echo htmlspecialchars($animal['data_inativacao']); ?>">
                    </div>
                    <div class="resumo-valor">
                        <strong>Resumo:</strong> Valor Compra - Saída (Venda ou Abate) = <span id="valor-final">R$ 0,00</span>
                    </div>

                </fieldset>
                
                <fieldset class="situacao-fieldset">
                    <legend>Situação do Animal</legend>
                    <label>Prenha:</label>
                    <select name="prenha">
                        <option value="" <?php echo ($animal['prenha'] == '') ? 'selected' : ''; ?>>Selecione</option>
                        <option value="S" <?php echo ($animal['prenha'] == 'S') ? 'selected' : ''; ?>>Sim</option>
                        <option value="N" <?php echo ($animal['prenha'] == 'N') ? 'selected' : ''; ?>>Não</option>
                    </select><br>
                    <label>Previsão de Parto:</label> <input type="date" name="previsao_parto" value="<?php echo htmlspecialchars($animal['previsao_parto']); ?>"><br>
                    <label>Data do Parto:</label> <input type="date" name="data_parto" value="<?php echo htmlspecialchars($animal['data_parto']); ?>"><br>
                    <label>Sexo da cria:</label>
                    <select name="sexo_bezerro">
                        <option value="" <?php echo ($animal['sexo_bezerro'] == '') ? 'selected' : ''; ?>>Selecione</option>
                        <option value="M" <?php echo ($animal['sexo_bezerro'] == 'M') ? 'selected' : ''; ?>>Macho</option>
                        <option value="F" <?php echo ($animal['sexo_bezerro'] == 'F') ? 'selected' : ''; ?>>Fêmea</option>
                    </select><br>
                    <label>Tipo de Cobertura:</label>
                    <select name="cobertura">
                        <option value="" <?php echo ($animal['cobertura'] == '') ? 'selected' : ''; ?>>Selecione</option>
                        <option value="TE" <?php echo ($animal['cobertura'] == 'TE') ? 'selected' : ''; ?>>TE</option>
                        <option value="FIV" <?php echo ($animal['cobertura'] == 'FIV') ? 'selected' : ''; ?>>FIV</option>
                    </select><br>
                </fieldset>
            </div>

            <div class="fieldset-row">
                <fieldset class="entrada-fieldset">
                    <legend>Entrada</legend>
                    <label>Origem:</label> <input type="text" name="origem" value="<?php echo htmlspecialchars($animal['entrada_origem']); ?>"><br>
                    <label>Data:</label> <input type="date" name="entrada_data" value="<?php echo htmlspecialchars($animal['entrada_data']); ?>"><br>
                    <label>Nº N.F:</label> <input type="text" name="entrada_nf" value="<?php echo htmlspecialchars($animal['entrada_nf']); ?>"><br>
                    <label>Natureza:</label> <input type="text" name="entrada_natureza" value="<?php echo htmlspecialchars($animal['entrada_natureza']); ?>"><br>
                    <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="entrada_valor" inputmode="decimal" value="<?php echo decimal_to_brl($animal['entrada_valor']); ?>"><br>
                    <label>Observações:</label> <input type="text" name="entrada_obs" value="<?php echo htmlspecialchars($animal['entrada_obs']); ?>"><br>
                </fieldset>

                <fieldset class="saida-fieldset">
                    <legend>Saída</legend>
                    <div class="row-flex">
                        <div class="col-flex">
                            <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="saida_valor" inputmode="decimal" value="<?php echo decimal_to_brl($animal['saida_valor']); ?>">
                        </div>
                        <div class="col-flex">
                            <label>Destino:</label> <input type="text" name="destino" value="<?php echo htmlspecialchars($animal['saida_destino']); ?>">
                        </div>
                    </div>
                    <label>Data:</label> <input type="date" name="saida_data" value="<?php echo htmlspecialchars($animal['saida_data']); ?>"><br>
                    <label>Nº N.F:</label> <input type="text" name="saida_nf" value="<?php echo htmlspecialchars($animal['saida_nf']); ?>"><br>
                    <label>Natureza:</label> <input type="text" name="saida_natureza" value="<?php echo htmlspecialchars($animal['saida_natureza']); ?>"><br>
                    <label>Observações:</label> <input type="text" name="saida_obs" value="<?php echo htmlspecialchars($animal['saida_obs']); ?>"><br>
                </fieldset>
            </div>

            <input type="submit" value="Atualizar Dados">
        </form>
    </div>

    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html> 