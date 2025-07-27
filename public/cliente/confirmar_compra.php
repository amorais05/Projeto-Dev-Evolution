<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Cupom;
use Src\Models\Compra;

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_produto = $_GET['id_produto'] ?? null;

if (!$id_produto) {
    $_SESSION['msg_erro'] = "Produto não informado.";
    header('Location: produtos_disponiveis.php');
    exit;
}

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../../db.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        throw new Exception("Produto não encontrado.");
    }

    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $id_cliente = $stmt->fetchColumn();

    if (!$id_cliente) {
        throw new Exception("Cliente não encontrado.");
    }
} catch (Exception $e) {
    $_SESSION['msg_erro'] = $e->getMessage();
    header('Location: produtos_disponiveis.php');
    exit;
}

$msg_cupom = $_SESSION['msg_cupom'] ?? '';
unset($_SESSION['msg_cupom']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Confirmar Compra</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 480px;
        }
        h2 {
            margin-bottom: 25px;
            color: #333;
            text-align: center;
            font-weight: 700;
        }
        p {
            margin-bottom: 15px;
            font-size: 16px;
            color: #444;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: #d93025;
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
        }
        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        #parcelasDiv {
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Confirmar Compra</h2>

    <p><strong>Produto:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
    <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>

    <?php if ($msg_cupom): ?>
        <p class="error"><?= htmlspecialchars($msg_cupom) ?></p>
    <?php endif; ?>

    <form action="finalizar_compra.php" method="post">
        <input type="hidden" name="id_produto" value="<?= (int)$id_produto ?>" />

        <label for="cupom">Cupom de Desconto (opcional):</label>
        <input type="text" id="cupom" name="cupom" maxlength="10" placeholder="Digite seu cupom">

        <label for="pagamento">Forma de Pagamento:</label>
        <select name="tipo_pagamento" id="pagamento" required onchange="mostrarParcelas(this.value)">
            <option value="">-- Selecione --</option>
            <option value="pix">Pix</option>
            <option value="boleto">Boleto</option>
            <option value="cartao">Cartão (até 3x com juros)</option>
        </select>

        <div id="parcelasDiv" style="display:none;">
            <label for="parcelas">Número de parcelas:</label>
            <select name="parcelas" id="parcelas">
                <option value="1">1x (sem juros)</option>
                <option value="2">2x (1,65% juros)</option>
                <option value="3">3x (1,65% juros)</option>
            </select>
        </div>

        <button type="submit">Confirmar Compra</button>
    </form>

    <a href="produtos_disponiveis.php">← Voltar para produtos</a>
</div>

<script>
    function mostrarParcelas(valor) {
        const parcelasDiv = document.getElementById('parcelasDiv');
        if (valor === 'cartao') {
            parcelasDiv.style.display = 'block';
        } else {
            parcelasDiv.style.display = 'none';
            document.getElementById('parcelas').value = '1';
        }
    }
</script>
</body>
</html>
