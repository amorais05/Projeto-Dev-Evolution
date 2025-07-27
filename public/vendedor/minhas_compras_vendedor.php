<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
use Src\Models\Compra;

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'vendedor') {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$compras = Compra::listarPorVendedor($id_usuario);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Compras dos Meus Clientes</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 16px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        p {
            margin-top: 24px;
            text-align: center;
            font-size: 15px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn-pdf {
            background-color: #007BFF; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .btn-pdf:hover {
            background-color: #0056b3;
        }

        /* Container flex para os botões */
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 15px; /* Espaço entre os botões */
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Compras dos Meus Clientes</h2>

    <?php if (!$compras): ?>
        <p>Seus clientes ainda não realizaram nenhuma compra.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Cliente</th>
                    <th>Data da Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nome_produto']) ?></td>
                        <td><?= htmlspecialchars($c['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($c['data_compra']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="btn-group">
        <a href="../vendedor/dashboard_vendedor.php" target="_blank" class="btn-pdf">← Voltar ao Dashboard</a>
        <a href="relatorio_compras_pdf.php" target="_blank" class="btn-pdf">📄 Baixar Relatório PDF</a>
    </div>
</div>

</body>
</html>
