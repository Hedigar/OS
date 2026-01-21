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
            'nome_completo' => filter_var($post['nome_completo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefone_principal' => filter_var($post['telefone_principal'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefone_secundario' => filter_var($post['telefone_secundario'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'documento' => filter_var($post['documento'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'tipo_pessoa' => filter_var($post['tipo_pessoa'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_nascimento' => filter_var($post['data_nascimento'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS) ?: null,
            'endereco_logradouro' => filter_var($post['endereco_logradouro'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_numero' => filter_var($post['endereco_numero'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_bairro' => filter_var($post['endereco_bairro'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'endereco_cidade' => filter_var($post['endereco_cidade'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
            'observacoes' => filter_var($post['observacoes'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
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
