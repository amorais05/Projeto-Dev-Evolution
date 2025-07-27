<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

// Exibe mensagens de sucesso ou erro, se houver
$msg_sucesso = $_SESSION['msg_sucesso'] ?? '';
$msg_erro = $_SESSION['msg_erro'] ?? '';
unset($_SESSION['msg_sucesso'], $_SESSION['msg_erro']);

require_once __DIR__ . '/../../vendor/autoload.php';
use Src\Models\Produto;

$id_cliente = $_SESSION['id_usuario']; // cliente logado

// Buscar todos os produtos com quantidade > 0
$produtos = Produto::listarProdutosDisponiveis();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Produtos Disponíveis</title>
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
            color: white;
            font-weight: bold;
        }
        a {
            color: #007BFF;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .msg-sucesso {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .msg-erro {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }
        p {
            margin-top: 25px;
        }
        p a {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Produtos Disponíveis</h2>

    <?php if ($msg_sucesso): ?>
        <p class="msg-sucesso"><?= htmlspecialchars($msg_sucesso) ?></p>
    <?php endif; ?>
    <?php if ($msg_erro): ?>
        <p class="msg-erro"><?= htmlspecialchars($msg_erro) ?></p>
    <?php endif; ?>

    <?php if (!$produtos): ?>
        <p>Nenhum produto disponível no momento.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Tipo</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= htmlspecialchars($p['descricao']) ?></td>
                        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['quantidade']) ?></td>
                        <td><?= htmlspecialchars($p['tipo']) ?></td>
                        <td>
                            <a href="confirmar_compra.php?id_produto=<?= urlencode($p['id']) ?>"
                               onclick="return confirm('Confirmar compra deste produto?')">Comprar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cliente.php">Voltar ao Dashboard</a></p>
</body>
</html>
