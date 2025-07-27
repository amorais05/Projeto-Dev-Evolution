<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Produto;
use Src\Models\Cliente;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['tipo'] !== 'cliente') {
    echo "<p style='color: red; font-weight: bold;'>Acesso negado. Esta página é restrita para clientes.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$produtos = Produto::listarTodos(); // Listar todos os produtos disponíveis para compra (ajuste se necessário)
$clientes = Cliente::listarPorUsuario($id_usuario); // Normalmente o cliente só tem ele mesmo, mas mantém o padrão
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Produtos para Comprar</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            color: #333;
        }
        h2 {
            color: #007BFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
            font-weight: bold;
        }
        form {
            margin: 0;
        }
        select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-right: 8px;
        }
        button {
            padding: 6px 12px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        small {
            color: #999;
            font-style: italic;
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
    <h2>Produtos disponíveis para compra</h2>

    <?php if (!$produtos): ?>
        <p>Nenhum produto disponível no momento.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Tipo</th>
                    <th>Comprar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= htmlspecialchars($p['descricao']) ?></td>
                        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['quantidade']) ?></td>
                        <td><?= htmlspecialchars($p['tipo']) ?></td>
                        <td>
                            <?php if ($p['quantidade'] > 0): ?>
                                <form action="realizar_compra.php" method="GET">
                                    <input type="hidden" name="id_produto" value="<?= $p['id'] ?>">
                                    <select name="id_cliente" required>
                                        <?php foreach ($clientes as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Comprar</button>
                                </form>
                            <?php else: ?>
                                <small>Indisponível</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cliente.php">Voltar ao Dashboard</a></p>
</body>
</html>
