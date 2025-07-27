<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Produto;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['tipo'] !== 'vendedor') {
    echo "<p style='color: red; font-weight: bold;'>Acesso negado. Esta página é restrita para vendedores.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$produtos = Produto::listarPorUsuario($id_usuario);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Meus Produtos</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 900px;
            width: 100%;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        a, a:visited {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-cadastrar {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 24px;
            background-color: white;
            color: #007BFF;
            border: 2px solid #007BFF;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-cadastrar:hover {
            background-color: #007BFF;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: 700;
        }
        td a {
            margin: 0 5px;
            color: #007BFF;
            font-weight: 600;
        }
        td a:hover {
            text-decoration: underline;
        }
        p.voltar {
            margin-top: 25px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Produtos cadastrados</h2>
        <p><a href="cadastro_produto.php" class="btn-cadastrar">Cadastrar novo produto</a></p>

        <?php if (!$produtos): ?>
            <p>Você não tem produtos cadastrados.</p>
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
                        <th>Ações</th>
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
                                <a href="editar_produto.php?id=<?= $p['id'] ?>">Editar</a> |
                                <a href="deletar_produto.php?id=<?= $p['id'] ?>" onclick="return confirm('Tem certeza que deseja deletar este produto?')">Deletar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="voltar"><a href="dashboard_vendedor.php">Voltar ao Dashboard</a></p>
    </div>
</body>
</html>
