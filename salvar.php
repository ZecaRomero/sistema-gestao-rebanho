<?php
session_start();
require 'db.php'; // Inclui o arquivo de conexão

// Função para converter valor monetário BRL (1.234,56) para formato de banco de dados (1234.56)
function brl_to_decimal($brl) {
    if (empty($brl)) {
        return null;
    }
    return floatval(str_replace(',', '.', str_replace('.', '', $brl)));
}

// Função para tratar datas vazias
function empty_to_null($value) {
    return empty($value) ? null : $value;
}

try {
    // --- Verificação de Duplicidade ---
    $rg = empty_to_null($_POST['rg'] ?? null);
    if ($rg) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM animais WHERE rg = :rg");
        $stmt->execute([':rg' => $rg]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['erro'] = "Erro: Já existe um animal com o RG '$rg' cadastrado.";
            header('Location: dashboard.php');
            exit;
        }
    }

    $sql = "INSERT INTO animais (
        serie, rg, nascimento, meses, raca, sexo, pai, avo_materno, mae_biologica, receptora, 
        ativo, data_inativacao, valor_adquirido, origem_compra, valor_abate, destino_abate, 
        valor_venda, destino_venda, entrada_origem, entrada_data, entrada_nf, entrada_natureza, 
        entrada_valor, entrada_obs, saida_valor, saida_destino, saida_data, saida_nf, 
        saida_natureza, saida_obs, prenha, previsao_parto, data_parto, sexo_bezerro, cobertura
    ) VALUES (
        :serie, :rg, :nascimento, :meses, :raca, :sexo, :pai, :avo_materno, :mae_biologica, :receptora, 
        :ativo, :data_inativacao, :valor_adquirido, :origem_compra, :valor_abate, :destino_abate, 
        :valor_venda, :destino_venda, :entrada_origem, :entrada_data, :entrada_nf, :entrada_natureza, 
        :entrada_valor, :entrada_obs, :saida_valor, :saida_destino, :saida_data, :saida_nf, 
        :saida_natureza, :saida_obs, :prenha, :previsao_parto, :data_parto, :sexo_bezerro, :cobertura
    )";

    $stmt = $pdo->prepare($sql);

    // Mapeamento dos campos do formulário para os parâmetros do SQL
    $stmt->bindValue(':serie', empty_to_null($_POST['serie'] ?? null));
    $stmt->bindValue(':rg', empty_to_null($_POST['rg'] ?? null));
    $stmt->bindValue(':nascimento', empty_to_null($_POST['nascimento'] ?? null));
    $stmt->bindValue(':meses', empty_to_null($_POST['meses'] ?? null));
    $stmt->bindValue(':raca', empty_to_null($_POST['raca'] ?? null));
    $stmt->bindValue(':sexo', empty_to_null($_POST['sexo'] ?? null));
    $stmt->bindValue(':pai', empty_to_null($_POST['pai'] ?? null));
    $stmt->bindValue(':avo_materno', empty_to_null($_POST['avo_materno'] ?? null));
    $stmt->bindValue(':mae_biologica', empty_to_null($_POST['mae_biologica'] ?? null));
    $stmt->bindValue(':receptora', empty_to_null($_POST['receptora'] ?? null));
    $stmt->bindValue(':ativo', empty_to_null($_POST['ativo'] ?? 'S'));
    $stmt->bindValue(':data_inativacao', empty_to_null($_POST['data_inativacao'] ?? null));
    $stmt->bindValue(':valor_adquirido', brl_to_decimal($_POST['valor_adquirido'] ?? null));
    $stmt->bindValue(':origem_compra', empty_to_null($_POST['origem_compra'] ?? null));
    $stmt->bindValue(':valor_abate', brl_to_decimal($_POST['valor_abate'] ?? null));
    $stmt->bindValue(':destino_abate', empty_to_null($_POST['destino_abate'] ?? null));
    $stmt->bindValue(':valor_venda', brl_to_decimal($_POST['valor_venda'] ?? null));
    $stmt->bindValue(':destino_venda', empty_to_null($_POST['destino_venda'] ?? null));
    
    // Campos do fieldset 'Entrada'
    $stmt->bindValue(':entrada_origem', empty_to_null($_POST['origem'] ?? null));
    $stmt->bindValue(':entrada_data', empty_to_null($_POST['entrada_data'] ?? null));
    $stmt->bindValue(':entrada_nf', empty_to_null($_POST['entrada_nf'] ?? null));
    $stmt->bindValue(':entrada_natureza', empty_to_null($_POST['entrada_natureza'] ?? null));
    $stmt->bindValue(':entrada_valor', brl_to_decimal($_POST['entrada_valor'] ?? null));
    $stmt->bindValue(':entrada_obs', empty_to_null($_POST['entrada_obs'] ?? null));

    // Campos do fieldset 'Saída'
    $stmt->bindValue(':saida_valor', brl_to_decimal($_POST['saida_valor'] ?? null));
    $stmt->bindValue(':saida_destino', empty_to_null($_POST['destino'] ?? null));
    $stmt->bindValue(':saida_data', empty_to_null($_POST['saida_data'] ?? null));
    $stmt->bindValue(':saida_nf', empty_to_null($_POST['saida_nf'] ?? null));
    $stmt->bindValue(':saida_natureza', empty_to_null($_POST['saida_natureza'] ?? null));
    $stmt->bindValue(':saida_obs', empty_to_null($_POST['saida_obs'] ?? null));

    // Campos do fieldset 'Situação do Animal'
    $stmt->bindValue(':prenha', empty_to_null($_POST['prenha'] ?? null));
    $stmt->bindValue(':previsao_parto', empty_to_null($_POST['previsao_parto'] ?? null));
    $stmt->bindValue(':data_parto', empty_to_null($_POST['data_parto'] ?? null));
    $stmt->bindValue(':sexo_bezerro', empty_to_null($_POST['sexo_bezerro'] ?? null));
    $stmt->bindValue(':cobertura', empty_to_null($_POST['cobertura'] ?? null));

    $stmt->execute();
    
    // Adiciona uma mensagem de sucesso na sessão
    $_SESSION['mensagem'] = "Animal salvo com sucesso!";

} catch (PDOException $e) {
    // Em caso de erro, armazena a mensagem de erro na sessão
    $_SESSION['erro'] = "Erro ao salvar animal: " . $e->getMessage();
}

// Redireciona de volta para o dashboard
header('Location: dashboard.php');
exit;
?> 