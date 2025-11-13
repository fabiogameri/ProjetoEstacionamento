<?php
// Conecta ao banco de dados
$db = new SQLite3(__DIR__ . '/../sqlite3/veiculos.db');

// Consulta para pegar as últimas adições
$results = $db->query('SELECT * FROM veiculos ORDER BY id DESC LIMIT 5');

$veiculos = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $veiculos[] = $row;
}

// Fechar a conexão
$db->close();

// Retorna os dados como JSON
header('Content-Type: application/json');
echo json_encode($veiculos);
?>
