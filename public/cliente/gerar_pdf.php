<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

if (!isset($_SESSION['ultima_compra'])) {
    header('Location: produtos_disponiveis.php');
    exit;
}

$compra = $_SESSION['ultima_compra'];

// Carregar logo como base64
$logoPath = __DIR__ . '/../../public/img/logo.png';
$logoHtml = '';
if (file_exists($logoPath)) {
    $imageData = base64_encode(file_get_contents($logoPath));
    $logoHtml = '<img src="data:image/png;base64,' . $imageData . '" width="150" style="margin-bottom: 30px;" />';
}

$html = '
<style>
    body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
    h1 { color: #007BFF; font-size: 28px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #cce5ff; color: #004085; }
    p.cupom { margin-top: 25px; font-style: italic; font-size: 14px; }
    p.thanks {
        margin-top: 50px;
        font-weight: bold;
        font-size: 18px;
        font-style: italic;
        color: #28a745;
    }
</style>

' . $logoHtml . '

<h1>Resumo da Compra</h1>

<table>
    <tr><th>Produto</th><td>' . htmlspecialchars($compra['produto']) . '</td></tr>
    <tr><th>Valor Original</th><td>R$ ' . number_format($compra['valor_original'], 2, ",", ".") . '</td></tr>
    <tr><th>Valor Final</th><td>R$ ' . number_format($compra['valor_final'], 2, ",", ".") . '</td></tr>
    <tr><th>Parcelas</th><td>' . htmlspecialchars($compra['parcelas']) . '</td></tr>
    <tr><th>Forma de pagamento</th><td>' . htmlspecialchars(ucfirst($compra['tipo_pagamento'])) . '</td></tr>
</table>

<p class="cupom"><strong>Cupom:</strong> ' . ($compra['cupom'] ?? 'Nenhum') . '</p>

<p class="thanks">Obrigado pela sua compra!</p>
';

// Criar Dompdf e gerar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nome do arquivo com produto e data/hora
$nomeArquivo = 'resumo_compra_' . preg_replace('/[^a-z0-9]/i', '_', strtolower($compra['produto'])) . '_' . date('Ymd_His') . '.pdf';

// Forçar download do PDF
$dompdf->stream($nomeArquivo, ["Attachment" => true]);

exit;
