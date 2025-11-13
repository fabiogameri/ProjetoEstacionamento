<?php
require 'conexaodb.php';

$db = new SQLite3(__DIR__ . '\..\sqlite3\veiculos.db');
$sql = "SELECT id, pessoa, marca, modelo, ano, cor, dia, mes, pago, data_renovacao FROM veiculos";
$result = $db->query($sql);

function obterNomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Meta Tag para o Viewport -->
    <title>Lista de Veículos</title>
    <link rel="stylesheet" href="../css/style_listagem.css">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
    <div class="table-container">
        <h1>Veículos Cadastrados</h1>
        <div class="veiculos-list">
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): 
                $dataAtual = new DateTime();
                $classeVencimento = '';
                $botaoAcao = '';
                $formAction = '';
                $textoBotao = '';

                // Verifica se há data de renovação
                if (!empty($row['data_renovacao'])) {
                    $dataRenovacao = new DateTime($row['data_renovacao']);
                    $intervalo = $dataRenovacao->diff($dataAtual)->days;

                    // Se passaram mais de 30 dias desde a renovação → expirada
                    if ($intervalo > 30) {
                        $classeVencimento = 'vencido';
                        $row['pago'] = 0; // Considerar como não pago
                        $formAction = 'renovar.php';
                        $textoBotao = 'Renovar Mensalidade';
                    } else {
                        // Ainda dentro do prazo
                        $formAction = 'cancelar_renovacao.php';
                        $textoBotao = 'Cancelar Renovação';
                    }
                } else {
                    // Nunca foi renovado → vencido
                    $classeVencimento = 'vencido';
                    $row['pago'] = 0;
                    $formAction = 'renovar.php';
                    $textoBotao = 'Renovar Mensalidade';
                }
            ?>
                <div class="veiculo-item <?php echo $classeVencimento; ?>">
                    <h2><?php echo htmlspecialchars($row['pessoa']); ?></h2>
                    <p><strong>Marca:</strong> <?php echo htmlspecialchars($row['marca']); ?></p>
                    <p><strong>Modelo:</strong> <?php echo htmlspecialchars($row['modelo']); ?></p>
                    <p><strong>Ano:</strong> <?php echo htmlspecialchars($row['ano']); ?></p>
                    <p><strong>Cor:</strong> <?php echo htmlspecialchars($row['cor']); ?></p>
                    <p><strong>Dia:</strong> <?php echo htmlspecialchars($row['dia']); ?></p>
                    <p><strong>Mês:</strong> <?php echo obterNomeMes($row['mes']); ?></p>
                    <p><strong>Pagamento:</strong> <?php echo $row['pago'] ? 'Pago' : 'Não Pago'; ?></p>
                    <p><strong>Data de Renovação:</strong> <?php echo htmlspecialchars($row['data_renovacao']); ?></p>

                    <form method="post" action="<?php echo $formAction; ?>">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit"><?php echo $textoBotao; ?></button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Rodapé com o botão centralizado -->
    <footer>
        <button class="voltar" onclick="window.location.href='../index.html'">Voltar ao Menu</button>
    </footer>
</body>
</body>
</html>

<?php
$db->close();
?>
