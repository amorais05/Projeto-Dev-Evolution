<?php
session_start();

if (!isset($_SESSION['ultima_compra'])) {
    header('Location: produtos_disponiveis.php');
    exit;
}

$compra = $_SESSION['ultima_compra'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Resumo da Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
            color: #333;
        }
        h2 {
            color: #007BFF;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin: 8px 0;
        }
        strong {
            color: #555;
        }
        form {
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            display: inline-block;
            margin-top: 25px;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Resumo da Compra</h2>

    <p><strong>Produto:</strong> <?= htmlspecialchars($compra['produto']) ?></p>
    <p><strong>Valor Original:</strong> R$ <?= number_format($compra['valor_original'], 2, ',', '.') ?></p>
    <p><strong>Valor Final:</strong> R$ <?= number_format($compra['valor_final'], 2, ',', '.') ?></p>
    <p><strong>Parcelas:</strong> <?= htmlspecialchars($compra['parcelas']) ?></p>
    <p><strong>Forma de pagamento:</strong> <?= htmlspecialchars(ucfirst($compra['tipo_pagamento'])) ?></p>
    <p><strong>Cupom:</strong> <?= $compra['cupom'] ?? 'Nenhum' ?></p>

    <form method="POST" action="gerar_pdf.php">
        <button type="submit">Baixar PDF da Compra</button>
    </form>

    <p><a href="produtos_disponiveis.php">Voltar aos produtos</a></p>
</body>
</html>
