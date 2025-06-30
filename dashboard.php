<?php
session_start();
if (empty($_SESSION['logado'])) {
    header('Location: index.html');
    exit;
}
// Restante do código permanece igual
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Animal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header-container">
        <h1>🐮 Sistema de Gestão de Rebanho</h1>
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
    <div class="container">
        <form action="salvar.php" method="POST">
            <fieldset class="cadastro-fieldset">
                <legend>Cadastro do Animal</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Série:</label> <input type="text" name="serie">
                    </div>
                    <div class="col-flex">
                        <label>RG:</label> <input type="text" name="rg">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Nascimento:</label> <input type="date" name="nascimento">
                    </div>
                    <div class="col-flex">
                        <label>Meses:</label> <input type="number" name="meses" min="0" readonly> <span id="idade-anos" class="idade-anos"></span>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex pequeno">
                        <label>Raça:</label>
                        <select name="raca" class="pequeno">
                            <option value="">Selecione</option>
                            <option value="Nelore">Nelore</option>
                            <option value="Brahman">Brahman</option>
                            <option value="Gir">Gir</option>
                            <option value="Receptoras">Receptoras</option>
                            <option value="Cruza">Cruza</option>
                            <option value="Nelore PA">Nelore PA</option>
                        </select>
                    </div>
                    <div class="col-flex">
                        <div id="sexo-buttons" class="sexo-buttons-central">
                            <label class="sexo-label-central">Sexo:</label>
                            <div class="sexo-btns-row">
                                <button type="button" class="sexo-btn sexo-macho" data-value="M" aria-label="Selecionar macho">Macho</button>
                                <button type="button" class="sexo-btn sexo-femea" data-value="F" aria-label="Selecionar fêmea">Fêmea</button>
                            </div>
                            <input type="hidden" name="sexo" id="sexo-input">
                            <span id="sexo-feedback" class="feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Pai:</label> <input type="text" name="pai">
                    </div>
                    <div class="col-flex">
                        <label>Avô Materno:</label> <input type="text" name="avo_materno">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Mãe Biológica:</label> <input type="text" name="mae_biologica">
                    </div>
                    <div class="col-flex">
                        <label>Receptora:</label> <input type="text" name="receptora">
                    </div>
                </div>
                <label>Ativo:</label>
                <div id="ativo-buttons">
                    <button type="button" class="ativo-btn ativo-sim" data-value="S">Sim</button>
                    <button type="button" class="ativo-btn ativo-nao" data-value="N">Não</button>
                    <input type="hidden" name="ativo" id="ativo-input">
                </div><br>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor Adquirido:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_adquirido" inputmode="decimal">
                    </div>
                    <div class="col-flex">
                        <label>Origem da Compra:</label> <input type="text" name="origem_compra">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor Abate:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_abate" inputmode="decimal">
                    </div>
                    <div class="col-flex">
                        <label>Destino Abate:</label> <input type="text" name="destino_abate">
                    </div>
                </div>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor Venda:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="valor_venda" inputmode="decimal">
                    </div>
                    <div class="col-flex">
                        <label>Destino Venda:</label> <input type="text" name="destino_venda">
                    </div>
                </div>
                <!-- Resumo do valor final -->
                <div class="resumo-valor">
                    <strong>Resumo:</strong> Valor da Compra - Valor de Venda = <span id="valor-final">R$ 0,00</span>
                </div>
            </fieldset>

            <fieldset class="entrada-fieldset">
                <legend>Entrada</legend>
                <label>Origem:</label> <input type="text" name="origem"><br>
                <label>Data:</label> <input type="date" name="entrada_data"><br>
                <label>Nº N.F:</label> <input type="text" name="entrada_nf"><br>
                <label>Natureza da Operação:</label> <input type="text" name="entrada_natureza"><br>
                <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais" name="entrada_valor" inputmode="decimal"><br>
                <label>Observações:</label> <input type="text" name="entrada_obs"><br>
            </fieldset>

            <fieldset class="saida-fieldset">
                <legend>Saída</legend>
                <div class="row-flex">
                    <div class="col-flex">
                        <label>Valor:</label> <span>R$</span><input type="text" class="valor-reais pequeno" name="saida_valor" inputmode="decimal">
                    </div>
                    <div class="col-flex">
                        <label>Destino:</label> <input type="text" name="destino">
                    </div>
                </div>
                <label>Data:</label> <input type="date" name="saida_data"><br>
                <label>Nº N.F:</label> <input type="text" name="saida_nf"><br>
                <label>Natureza da Operação:</label> <input type="text" name="saida_natureza"><br>
                <label>Observações:</label> <input type="text" name="saida_obs"><br>
            </fieldset>

            <fieldset class="situacao-fieldset">
                <legend>Situação do Animal</legend>
                <label>Prenha:</label>
                <select name="prenha">
                    <option value="">Selecione</option>
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select><br>
                <label>Previsão de Parto:</label> <input type="date" name="previsao_parto"><br>
                <label>Data do Parto:</label> <input type="date" name="data_parto"><br>
                <label>Sexo da cria:</label>
                <select name="sexo_bezerro">
                    <option value="">Selecione</option>
                    <option value="M">Macho</option>
                    <option value="F">Fêmea</option>
                </select><br>
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

    <script src="script.js"></script>
</body>
</html> 