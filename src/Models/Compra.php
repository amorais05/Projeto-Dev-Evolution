<?php
namespace Src\Models;

use PDO;
use PDOException;
use Exception;
use Src\Models\Conexao;

class Compra
{
    // Usa a conexão única da classe Conexao
    private static function conectar()
    {
        return Conexao::conectar();
    }

    public static function registrar($id_produto, $id_usuario_cliente)
    {
        $pdo = self::conectar();
        $pdo->beginTransaction();

        // Buscar o ID do cliente a partir do usuário logado
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id_usuario = :id_usuario");
        $stmt->execute([':id_usuario' => $id_usuario_cliente]);
        $id_cliente = $stmt->fetchColumn();

        if (!$id_cliente) {
            $pdo->rollBack();
            throw new Exception("Cliente não encontrado para o usuário id {$id_usuario_cliente}.");
        }

        // Buscar o produto e verificar disponibilidade
        $stmt = $pdo->prepare("SELECT quantidade, reservado, data_reserva, id_usuario FROM produtos WHERE id = :id_produto");
        $stmt->execute([':id_produto' => $id_produto]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            $pdo->rollBack();
            throw new Exception("Produto não encontrado.");
        }

        $disponivel = $produto['quantidade'] > 0;

        if ($produto['quantidade'] == 1 && $produto['reservado']) {
            $tempoReserva = (int) $produto['data_reserva'];
            if (time() - $tempoReserva > 120) {
                $pdo->prepare("UPDATE produtos SET reservado = 0, data_reserva = NULL WHERE id = :id_produto")
                    ->execute([':id_produto' => $id_produto]);
                $pdo->rollBack();
                throw new Exception("Produto reservado recentemente. Tente novamente mais tarde.");
            }
        }

        if (!$disponivel) {
            $pdo->rollBack();
            throw new Exception("Produto indisponível.");
        }

        $id_vendedor = $produto['id_usuario'];

        // Inserir a compra
        $stmt = $pdo->prepare("INSERT INTO compras (id_produto, id_usuario, id_cliente) VALUES (:id_produto, :id_usuario, :id_cliente)");
        $stmt->execute([
            ':id_produto' => $id_produto,
            ':id_usuario' => $id_vendedor,
            ':id_cliente' => $id_cliente
        ]);

        // Atualizar estoque
        $stmt = $pdo->prepare("UPDATE produtos SET quantidade = quantidade - 1, reservado = 0, data_reserva = NULL WHERE id = :id_produto");
        $stmt->execute([':id_produto' => $id_produto]);

        $pdo->commit();
        return true;
    }

    public static function listarPorVendedor($id_vendedor)
    {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("
            SELECT 
                p.nome AS nome_produto, 
                c.data_compra,
                cli.nome AS nome_cliente
            FROM compras c
            JOIN produtos p ON c.id_produto = p.id
            JOIN clientes cli ON c.id_cliente = cli.id
            WHERE p.id_usuario = :id_vendedor
            ORDER BY c.data_compra DESC
        ");
        $stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorCliente($id_cliente)
    {
        $pdo = self::conectar();

        $stmt = $pdo->prepare("
            SELECT 
                p.nome AS nome_produto,
                c.data_compra
            FROM compras c
            JOIN produtos p ON c.id_produto = p.id
            WHERE c.id_cliente = :id_cliente
            ORDER BY c.data_compra DESC
        ");
        $stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function buscarIdClientePorUsuario($id_usuario)
    {
        $pdo = self::conectar();
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id_usuario = :id_usuario");
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchColumn();
    }

    public static function registrarComPagamento($id_produto, $id_cliente, $valor_original, $valor_final, $tipo_pagamento, $parcelas, $cupom = null)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            // Validar cliente
            $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id = :id_cliente");
            $stmt->execute([':id_cliente' => $id_cliente]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Cliente não encontrado para o id {$id_cliente}.");
            }

            // Buscar produto e verificar disponibilidade
            $stmt = $pdo->prepare("SELECT quantidade, reservado, data_reserva, id_usuario FROM produtos WHERE id = :id_produto");
            $stmt->execute([':id_produto' => $id_produto]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new Exception("Produto não encontrado.");
            }

            $disponivel = $produto['quantidade'] > 0;

            if ($produto['quantidade'] == 1 && $produto['reservado']) {
                $tempoReserva = (int) $produto['data_reserva'];
                if (time() - $tempoReserva > 120) {
                    $pdo->prepare("UPDATE produtos SET reservado = 0, data_reserva = NULL WHERE id = :id_produto")
                        ->execute([':id_produto' => $id_produto]);
                    throw new Exception("Produto reservado recentemente. Tente novamente mais tarde.");
                }
            }

            if (!$disponivel) {
                throw new Exception("Produto indisponível.");
            }

            $id_vendedor = $produto['id_usuario'];

            // Inserir compra com os dados de pagamento
            $stmt = $pdo->prepare("
                INSERT INTO compras 
                (id_produto, id_usuario, id_cliente, data_compra, valor_original, valor_final, tipo_pagamento, parcelas, cupom)
                VALUES 
                (:id_produto, :id_usuario, :id_cliente, CURRENT_TIMESTAMP, :valor_original, :valor_final, :tipo_pagamento, :parcelas, :cupom)
            ");

            $stmt->execute([
                ':id_produto' => $id_produto,
                ':id_usuario' => $id_vendedor,
                ':id_cliente' => $id_cliente,
                ':valor_original' => $valor_original,
                ':valor_final' => $valor_final,
                ':tipo_pagamento' => $tipo_pagamento,
                ':parcelas' => $parcelas,
                ':cupom' => $cupom
            ]);

            // Atualizar estoque (garantindo que não fique negativo)
            $stmt = $pdo->prepare("
                UPDATE produtos 
                SET quantidade = CASE WHEN quantidade > 0 THEN quantidade - 1 ELSE 0 END, reservado = 0, data_reserva = NULL 
                WHERE id = :id_produto
            ");
            $stmt->execute([':id_produto' => $id_produto]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
