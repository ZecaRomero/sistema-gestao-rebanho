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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Lógica de verificação de duplicidade (ignorando o próprio animal)
    $rg = empty_to_null($_POST['rg'] ?? null);
    if ($rg) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM animais WHERE rg = :rg AND id != :id");
        $stmt->execute([':rg' => $rg, ':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['erro'] = "Erro: Já existe outro animal com o RG '$rg' cadastrado.";
            header("Location: editar_animal.php?id=$id"); // Volta para a edição
            exit;
        }
    }

    try {
        $sql = "UPDATE animais SET 
            serie = :serie, rg = :rg, nascimento = :nascimento, meses = :meses, raca = :raca, sexo = :sexo, 
            pai = :pai, avo_materno = :avo_materno, mae_biologica = :mae_biologica, receptora = :receptora, 
            ativo = :ativo, data_inativacao = :data_inativacao, valor_adquirido = :valor_adquirido, 
            origem_compra = :origem_compra, valor_abate = :valor_abate, destino_abate = :destino_abate, 
            valor_venda = :valor_venda, destino_venda = :destino_venda, entrada_origem = :entrada_origem, 
            entrada_data = :entrada_data, entrada_nf = :entrada_nf, entrada_natureza = :entrada_natureza, 
            entrada_valor = :entrada_valor, entrada_obs = :entrada_obs, saida_valor = :saida_valor, 
            saida_destino = :saida_destino, saida_data = :saida_data, saida_nf = :saida_nf, 
            saida_natureza = :saida_natureza, saida_obs = :saida_obs, prenha = :prenha, 
            previsao_parto = :previsao_parto, data_parto = :data_parto, sexo_bezerro = :sexo_bezerro, 
            cobertura = :cobertura
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        // Mapeamento completo dos campos
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':serie', empty_to_null($_POST['serie'] ?? null));
        $stmt->bindValue(':rg', $rg);
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
        $stmt->bindValue(':entrada_origem', empty_to_null($_POST['origem'] ?? null));
        $stmt->bindValue(':entrada_data', empty_to_null($_POST['entrada_data'] ?? null));
        $stmt->bindValue(':entrada_nf', empty_to_null($_POST['entrada_nf'] ?? null));
        $stmt->bindValue(':entrada_natureza', empty_to_null($_POST['entrada_natureza'] ?? null));
        $stmt->bindValue(':entrada_valor', brl_to_decimal($_POST['entrada_valor'] ?? null));
        $stmt->bindValue(':entrada_obs', empty_to_null($_POST['entrada_obs'] ?? null));
        $stmt->bindValue(':saida_valor', brl_to_decimal($_POST['saida_valor'] ?? null));
        $stmt->bindValue(':saida_destino', empty_to_null($_POST['destino'] ?? null));
        $stmt->bindValue(':saida_data', empty_to_null($_POST['saida_data'] ?? null));
        $stmt->bindValue(':saida_nf', empty_to_null($_POST['saida_nf'] ?? null));
        $stmt->bindValue(':saida_natureza', empty_to_null($_POST['saida_natureza'] ?? null));
        $stmt->bindValue(':saida_obs', empty_to_null($_POST['saida_obs'] ?? null));
        $stmt->bindValue(':prenha', empty_to_null($_POST['prenha'] ?? null));
        $stmt->bindValue(':previsao_parto', empty_to_null($_POST['previsao_parto'] ?? null));
        $stmt->bindValue(':data_parto', empty_to_null($_POST['data_parto'] ?? null));
        $stmt->bindValue(':sexo_bezerro', empty_to_null($_POST['sexo_bezerro'] ?? null));
        $stmt->bindValue(':cobertura', empty_to_null($_POST['cobertura'] ?? null));

        $stmt->execute();
        
        $_SESSION['mensagem'] = "Animal (ID: $id) atualizado com sucesso!";

    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao atualizar animal: " . $e->getMessage();
    }

    header('Location: lista_animais.php');
    exit;
}
?> 