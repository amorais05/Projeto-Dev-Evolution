<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';

use Src\Models\Conexao;
use Src\Models\Compra;
use Src\Models\Cupom;

// Verifica se usuário está logado e é cliente
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_produto = $_POST['id_produto'] ?? null;
$cupom = strtoupper(trim($_POST['cupom'] ?? ''));
$tipo_pagamento = $_POST['tipo_pagamento'] ?? '';
$parcelas = (int) ($_POST['parcelas'] ?? 1);

// Valida campos obrigatórios
if (!$id_produto || !$tipo_pagamento) {
    $_SESSION['msg_erro'] = "Produto ou forma de pagamento não informados.";
    header('Location: produtos_disponiveis.php');
    exit;
}

try {
    // Conexão com o banco via classe Conexao
    $pdo = Conexao::conectar();

    // Busca ID do cliente relacionado ao usuário
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $id_cliente = $stmt->fetchColumn();

    if (!$id_cliente) {
        throw new Exception("Cliente não encontrado.");
    }

    // Busca dados do produto
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id_produto]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        throw new Exception("Produto não encontrado.");
    }

    // Validações do parcelamento e forma de pagamento
    if ($tipo_pagamento !== 'cartao' && $parcelas !== 1) {
        throw new Exception("Parcelamento disponível apenas para pagamento por cartão.");
    }
    if ($tipo_pagamento === 'cartao' && ($parcelas < 1 || $parcelas > 3)) {
        throw new Exception("Parcelamento inválido. Escolha até 3 parcelas.");
    }

    $valor_original = (float) $produto['preco'];

    // Valida cupom e calcula desconto
    $desconto = 0;
    if ($cupom !== '') {
        $percentual = Cupom::validarCupom($cupom, $id_cliente);
        $desconto = ($percentual / 100) * $valor_original;
    }
    $valor_com_desconto = $valor_original - $desconto;

    // Calcula juros para parcelamento (1,65% por parcela adicional)
    $juros = 0;
    if ($parcelas > 1) {
        $juros = ($parcelas - 1) * 0.0165 * $valor_com_desconto;
    }

    $valor_final = $valor_com_desconto + $juros;

    // Registra compra com todos os detalhes
    $registrar = Compra::registrarComPagamento(
        $id_produto,
        $id_cliente,
        $valor_original,
        $valor_final,
        $tipo_pagamento,
        $parcelas,
        $cupom !== '' ? strtoupper($cupom) : null
    );

    if (!$registrar) {
        throw new Exception("Erro ao registrar compra.");
    }

    // Registra uso do cupom, se aplicável
    if ($cupom !== '') {
        Cupom::registrarUso($cupom, $id_cliente);
    }

    // Mensagem de sucesso com valores formatados
    $_SESSION['msg_sucesso'] = "Compra realizada com sucesso! Valor final: R$ " . number_format($valor_final, 2, ',', '.') .
        ($parcelas > 1 ? " em {$parcelas}x (juros incluídos)." : "") .
        " Forma de pagamento: " . ucfirst($tipo_pagamento);

    // Guarda dados da última compra para página de resumo e geração de PDF
    $_SESSION['ultima_compra'] = [
        'produto' => $produto['nome'],
        'valor_original' => $valor_original,
        'valor_final' => $valor_final,
        'parcelas' => $parcelas,
        'tipo_pagamento' => $tipo_pagamento,
        'cupom' => $cupom !== '' ? strtoupper($cupom) : null,
        'cliente_id' => $id_cliente,
        'vendedor_id' => $produto['id_usuario'],
    ];

    // Redireciona para resumo da compra
    header('Location: resumo_compra.php');
    exit;

} catch (Exception $e) {
    $_SESSION['msg_erro'] = $e->getMessage();
    header('Location: confirmar_compra.php?id_produto=' . urlencode($id_produto));
    exit;
}
