<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
use Src\Models\Compra;

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Busca o id_cliente correspondente ao usuário logado
$pdo = new PDO('sqlite:' . __DIR__ . '/../../db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT id FROM clientes WHERE id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);
$id_cliente = $stmt->fetchColumn();

if (!$id_cliente) {
    echo "<p>Cliente não encontrado.</p>";
    exit;
}

$compras = Compra::listarPorCliente($id_cliente);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Minhas Compras</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 700px;
            margin: 40px auto;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Minhas Compras</h2>

    <?php if (!$compras): ?>
        <p>Você ainda não realizou nenhuma compra.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Data da Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nome_produto']) ?></td>
                        <td><?= htmlspecialchars($c['data_compra']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cliente.php">Voltar ao Dashboard</a></p>
</body>
</html>
