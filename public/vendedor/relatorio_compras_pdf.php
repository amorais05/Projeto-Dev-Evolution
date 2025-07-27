<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Src\Models\Compra;

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'vendedor') {
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$compras = Compra::listarPorVendedor($id_usuario);

$dompdf = new Dompdf();

$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Compras</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #007BFF; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        td { text-align: left; }
    </style>
</head>
<body>
    <h2>Relatório de Compras dos Meus Clientes</h2>';

if (!$compras) {
    $html .= '<p>Nenhuma compra encontrada.</p>';
} else {
    $html .= '<table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Cliente</th>
                <th>Data da Compra</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($compras as $c) {
        $html .= '<tr>
            <td>' . htmlspecialchars($c['nome_produto']) . '</td>
            <td>' . htmlspecialchars($c['nome_cliente']) . '</td>
            <td>' . htmlspecialchars($c['data_compra']) . '</td>
        </tr>';
    }
    $html .= '</tbody></table>';
}

$html .= '</body></html>';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("relatorio_compras.pdf", ["Attachment" => false]);
exit;
