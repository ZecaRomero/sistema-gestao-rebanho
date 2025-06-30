<?php
require 'db.php';

// Adicionar BOM para UTF-8
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="animais.xls"');
header('Pragma: no-cache');
header('Expires: 0');
echo chr(0xEF).chr(0xBB).chr(0xBF); // BOM UTF-8

// Buscar todos os animais
$animais = $pdo->query("SELECT * FROM animais ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

if (!$animais) {
    echo 'Nenhum animal cadastrado.';
    exit;
}

// Descobrir colunas não vazias
$colunas = array_keys($animais[0]);
$colunas_nao_vazias = [];
foreach ($colunas as $col) {
    foreach ($animais as $animal) {
        if (isset($animal[$col]) && $animal[$col] !== '' && $animal[$col] !== null) {
            $colunas_nao_vazias[] = $col;
            break;
        }
    }
}

// Cabeçalho
function label_coluna($col) {
    $map = [
        'id' => 'ID',
        'serie' => 'Série',
        'rg' => 'RG',
        'raca' => 'Raça',
        'sexo' => 'Sexo',
        'ativo' => 'Status',
        'valor_adquirido' => 'Valor Adquirido',
        'origem_compra' => 'Origem da Compra',
        'valor_abate' => 'Valor Abate',
        'destino_abate' => 'Destino Abate',
        'valor_venda' => 'Valor Venda',
        'destino_venda' => 'Destino Venda',
        'entrada_origem' => 'Entrada Origem',
        'entrada_destino' => 'Entrada Destino',
        'entrada_data' => 'Entrada Data',
        'entrada_nf' => 'Entrada NF',
        'entrada_natureza' => 'Entrada Natureza',
        'entrada_valor' => 'Entrada Valor',
        'entrada_obs' => 'Entrada Observações',
        'saida_valor' => 'Saída Valor',
        'saida_destino' => 'Saída Destino',
        'saida_data' => 'Saída Data',
        'saida_nf' => 'Saída NF',
        'saida_natureza' => 'Saída Natureza',
        'saida_obs' => 'Saída Observações',
        'prenha' => 'Prenha',
        'previsao_parto' => 'Previsão Parto',
        'data_parto' => 'Data Parto',
        'sexo_bezerro' => 'Sexo da Cria',
        'cobertura' => 'Cobertura',
        // Adicione outros campos se necessário
    ];
    return $map[$col] ?? ucfirst(str_replace('_', ' ', $col));
}

echo "<table border='1'>";
echo "<tr>";
foreach ($colunas_nao_vazias as $col) {
    echo "<th>" . label_coluna($col) . "</th>";
}
echo "</tr>";
foreach ($animais as $animal) {
    echo "<tr>";
    foreach ($colunas_nao_vazias as $col) {
        $val = $animal[$col];
        if ($col === 'sexo') $val = ($val === 'M' ? 'Macho' : ($val === 'F' ? 'Fêmea' : ''));
        if ($col === 'ativo') $val = ($val === 'S' ? 'Ativo' : ($val === 'N' ? 'Inativo' : ''));
        if (is_numeric($val) && strpos($col, 'valor') !== false) $val = number_format($val, 2, ',', '.');
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "</tr>";
}
echo "</table>"; 