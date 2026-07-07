<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Equipamento;
use App\Models\AtendimentoExterno;

class ClienteService
{
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function buildSearchFilters(?string $termo): array
    {
        $whereClause = '';
        $params = [];
        if ($termo) {
            $whereClause = "nome_completo LIKE :term_nome OR documento LIKE :term_documento";
            $params['term_nome'] = "%{$termo}%";
            $params['term_documento'] = "%{$termo}%";
        }
        return [$whereClause, $params];
    }

    public function documentoExistente(?string $documento): ?int
    {
        if (empty($documento)) return null;
        $docLimpo = preg_replace('/\D/', '', $documento);
        $sql = "SELECT id FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 LIMIT 1";
        $stmt = $this->clienteModel->getConnection()->prepare($sql);
        $stmt->execute(['documento' => $docLimpo]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }

    public function documentoExistenteOutroCliente(?string $documento, int $id): bool
    {
        if (empty($documento)) return false;
        $docLimpo = preg_replace('/\D/', '', $documento);
        $sql = "SELECT id FROM clientes WHERE REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') = :documento AND ativo = 1 AND id != :id LIMIT 1";
        $stmt = $this->clienteModel->getConnection()->prepare($sql);
        $stmt->execute(['documento' => $docLimpo, 'id' => $id]);
        return (bool)$stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function normalizePostData(array $post): array
    {
        return [
            'nome_completo' => htmlspecialchars(trim($post['nome_completo'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'telefone_principal' => htmlspecialchars(trim($post['telefone_principal'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'telefone_secundario' => htmlspecialchars(trim($post['telefone_secundario'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'email' => filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'documento' => htmlspecialchars(trim($post['documento'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'tipo_pessoa' => htmlspecialchars(trim($post['tipo_pessoa'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'data_nascimento' => htmlspecialchars(trim($post['data_nascimento'] ?? ''), ENT_QUOTES, 'UTF-8') ?: null,
            'endereco_logradouro' => htmlspecialchars(trim($post['endereco_logradouro'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'endereco_numero' => htmlspecialchars(trim($post['endereco_numero'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'endereco_bairro' => htmlspecialchars(trim($post['endereco_bairro'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'endereco_cidade' => htmlspecialchars(trim($post['endereco_cidade'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'observacoes' => htmlspecialchars(trim($post['observacoes'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'lista_negra' => isset($post['lista_negra']) ? 1 : 0,
        ];
    }

    public function obterDadosVisualizacao(int $id): array
    {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) return [];

        $osModel = new OrdemServico();
        $equipamentoModel = new Equipamento();
        $atendimentoExternoModel = new AtendimentoExterno();

        return [
            'cliente' => $cliente,
            'historicoOS' => $osModel->findByClienteId($id),
            'equipamentos' => $equipamentoModel->findByClienteId($id),
            'historicoExterno' => $atendimentoExternoModel->findByClienteId($id)
        ];
    }

    public function gerarDebitos(int $clienteId): array
    {
        $db = (new OrdemServico())->getConnection();
        $sqlOS = "SELECT os.id, os.created_at, os.valor_total_os, os.status_pagamento, 
                         os.defeito_relatado, os.laudo_tecnico,
                         s.nome as status_nome, s.cor as status_cor,
                         e.modelo as equipamento_modelo,
                         (SELECT SUM(desconto) FROM itens_ordem_servico WHERE ordem_servico_id = os.id AND ativo = 1) as valor_desconto
                  FROM ordens_servico os
                  JOIN status_os s ON os.status_atual_id = s.id
                  LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                  WHERE os.cliente_id = :cid AND os.ativo = 1 AND (os.status_pagamento IS NULL OR os.status_pagamento != 'pago')
                  ORDER BY os.id DESC";
        $stmtOS = $db->prepare($sqlOS);
        $stmtOS->execute(['cid' => $clienteId]);
        $debitosOS = $stmtOS->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $sqlAE = "SELECT ae.id, ae.data_agendada, ae.pagamento, ae.valor_deslocamento, ae.descricao_problema, ae.detalhes_servico
                  FROM atendimentos_externos ae
                  WHERE ae.cliente_id = :cid AND (ae.pagamento IS NULL OR ae.pagamento != 'pago') AND ae.ativo = 1
                  ORDER BY ae.id DESC";
        $stmtAE = $db->prepare($sqlAE);
        $stmtAE->execute(['cid' => $clienteId]);
        $debitosAE = $stmtAE->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $service = new \App\Services\AtendimentoService();
        $aeDetalhados = [];
        foreach ($debitosAE as $row) {
            $det = $service->obterDetalhesVisualizacao((int)$row['id']);
            $aeDetalhados[] = array_merge($row, [
                'valor_total' => $det['valor_total'] ?? (float)($row['valor_deslocamento'] ?? 0),
                'valor_desconto' => $det['valor_desconto'] ?? 0 
            ]);
        }

        return [$debitosOS, $aeDetalhados];
    }
}
