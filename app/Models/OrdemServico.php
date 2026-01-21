<?php

namespace App\Models;

use App\Core\Model;

class OrdemServico extends Model
{
    protected string $table = 'ordens_servico';
    
    /**
     * Retorna uma OS com dados do cliente, status e equipamento.
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT os.*, 
                       c.nome_completo as cliente_nome, 
                       c.documento as cliente_documento,
                       c.telefone_principal as cliente_telefone,
                       s.nome as status_nome, 
                       s.cor as status_cor,
                       e.tipo as equipamento_tipo,
                       e.marca as equipamento_marca,
                       e.modelo as equipamento_modelo,
                       e.serial as equipamento_serial,
                       e.senha as equipamento_senha,
                       e.acessorios as equipamento_acessorios,
                       e.possui_fonte as equipamento_fonte,
                       e.sn_fonte as equipamento_sn_fonte
                    FROM {$this->table} os
                    JOIN clientes c ON os.cliente_id = c.id
                    JOIN status_os s ON os.status_atual_id = s.id
                    LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.id = :id AND os.ativo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Ajustado para ser compatível com App\Core\Model
     */
    public function countAll(string $whereClause = '', array $params = []): int
    {
        // Se a chamada vier do nosso controller com a string de busca personalizada
        if (empty($whereClause) && isset($params['custom_search'])) {
            $search = $params['custom_search'];
            $sql = "SELECT COUNT(*) as total 
                    FROM {$this->table} os
                    JOIN clientes c ON os.cliente_id = c.id
                    WHERE os.ativo = 1";
            
            $execParams = [];
            if (!empty($search)) {
                if (is_numeric($search)) {
                    $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
                    $execParams['search_id'] = $search;
                    $execParams['search_nome'] = "%{$search}%";
                } else {
                    $sql .= " AND c.nome_completo LIKE :search_nome";
                    $execParams['search_nome'] = "%{$search}%";
                }
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($execParams);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        }

        // Caso contrário, usa o comportamento padrão da classe pai
        return parent::countAll($whereClause, $params);
    }

    /**
     * BUSCA PAGINADA COM DETALHES
     */
    public function getAllWithDetailsPaginado(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor,
                       e.modelo as equipamento_modelo
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.ativo = 1";
        
        $params = [];
        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
                $params['search_id'] = $search;
                $params['search_nome'] = "%{$search}%";
            } else {
                $sql .= " AND c.nome_completo LIKE :search_nome";
                $params['search_nome'] = "%{$search}%";
            }
        }

        $sql .= " ORDER BY os.id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }
        
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Versão com filtros adicionais para a listagem.
     */
    public function countAllWithDetailsFiltered(string $search = '', array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.ativo = 1";
        $params = [];
        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
                $params[':search_id'] = $search;
                $params[':search_nome'] = "%{$search}%";
            } else {
                $sql .= " AND c.nome_completo LIKE :search_nome";
                $params[':search_nome'] = "%{$search}%";
            }
        }
        if (!empty($filters['status_id'])) {
            $sql .= " AND os.status_atual_id = :status_id";
            $params[':status_id'] = (int)$filters['status_id'];
        }
        if (!empty($filters['status_pagamento'])) {
            $sql .= " AND os.status_pagamento = :status_pagamento";
            $params[':status_pagamento'] = $filters['status_pagamento'];
        }
        if (!empty($filters['status_entrega'])) {
            $sql .= " AND os.status_entrega = :status_entrega";
            $params[':status_entrega'] = $filters['status_entrega'];
        }
        if (!empty($filters['sem_atualizacao_dias'])) {
            $sql .= " AND DATEDIFF(NOW(), COALESCE((SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id), os.created_at)) >= :sem_dias";
            $params[':sem_dias'] = (int)$filters['sem_atualizacao_dias'];
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    public function getAllWithDetailsPaginadoFiltered(string $search = '', int $limit = 10, int $offset = 0, array $filters = []): array
    {
        $sql = "SELECT os.*, c.nome_completo as cliente_nome, s.nome as status_nome, s.cor as status_cor,
                       e.modelo as equipamento_modelo
                FROM {$this->table} os
                JOIN clientes c ON os.cliente_id = c.id
                JOIN status_os s ON os.status_atual_id = s.id
                LEFT JOIN equipamentos e ON os.equipamento_id = e.id
                WHERE os.ativo = 1";
        $params = [];
        if (!empty($search)) {
            if (is_numeric($search)) {
                $sql .= " AND (os.id = :search_id OR c.nome_completo LIKE :search_nome)";
                $params[':search_id'] = $search;
                $params[':search_nome'] = "%{$search}%";
            } else {
                $sql .= " AND c.nome_completo LIKE :search_nome";
                $params[':search_nome'] = "%{$search}%";
            }
        }
        if (!empty($filters['status_id'])) {
            $sql .= " AND os.status_atual_id = :status_id";
            $params[':status_id'] = (int)$filters['status_id'];
        }
        if (!empty($filters['status_pagamento'])) {
            $sql .= " AND os.status_pagamento = :status_pagamento";
            $params[':status_pagamento'] = $filters['status_pagamento'];
        }
        if (!empty($filters['status_entrega'])) {
            $sql .= " AND os.status_entrega = :status_entrega";
            $params[':status_entrega'] = $filters['status_entrega'];
        }
        if (!empty($filters['sem_atualizacao_dias'])) {
            $sql .= " AND DATEDIFF(NOW(), COALESCE((SELECT MAX(h.created_at) FROM ordens_servico_status_historico h WHERE h.ordem_servico_id = os.id), os.created_at)) >= :sem_dias";
            $params[':sem_dias'] = (int)$filters['sem_atualizacao_dias'];
        }
        $sql .= " ORDER BY os.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    public function getAllWithDetails(string $search = ''): array
    {
        return $this->getAllWithDetailsPaginado($search, 999999, 0);
    }

    public function updateTotals(int $osId, array $itens): bool
    {
        $totalProdutos = 0.00;
        $totalServicos = 0.00;
        $totalDesconto = 0.00;

        foreach ($itens as $item) {
            $tipo = $item['tipo_item'] ?? $item['tipo'] ?? '';
            if ($tipo === 'produto') {
                $totalProdutos += $item['valor_total'];
            } elseif ($tipo === 'servico') {
                $totalServicos += $item['valor_total'];
            }
            $totalDesconto += (float)($item['desconto'] ?? 0);
        }

        $totalOS = $totalProdutos + $totalServicos;

        $sql = "UPDATE {$this->table} SET
                valor_total_produtos = :vtp,
                valor_total_servicos = :vts,
                valor_total_os = :vto,
                valor_desconto = :vd
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'vtp' => $totalProdutos,
            'vts' => $totalServicos,
            'vto' => $totalOS,
            'vd'  => $totalDesconto,
            'id'  => $osId
        ]);
    }

    public function findByClienteId(int $clienteId): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.cliente_id = :cliente_id AND os.ativo = 1
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByEquipamentoId(int $equipamentoId): array
    {
        $sql = "SELECT os.*, s.nome as status_nome, s.cor as status_cor
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                WHERE os.equipamento_id = :equipamento_id AND os.ativo = 1
                ORDER BY os.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['equipamento_id' => $equipamentoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAlertasDashboard(
        int $diasSemAtualizacao = 2,
        int $diasAbertasAtraso = 3,
        int $diasFinalizadasRecentes = 2
    ): array {
        $sql = "SELECT 
                    os.id,
                    os.status_atual_id,
                    os.created_at,
                    os.status_pagamento,
                    os.status_entrega,
                    c.nome_completo as cliente_nome,
                    c.telefone_principal as cliente_telefone,
                    s.nome as status_nome,
                    s.cor as status_cor,
                    COALESCE(
                        (SELECT MAX(h.created_at) 
                         FROM ordens_servico_status_historico h 
                         WHERE h.ordem_servico_id = os.id),
                        os.created_at
                    ) as ultima_atualizacao
                FROM {$this->table} os
                JOIN status_os s ON os.status_atual_id = s.id
                JOIN clientes c ON os.cliente_id = c.id
                WHERE os.ativo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $now = new \DateTimeImmutable();
        $alertas = [];

        foreach ($rows as $row) {
            if (empty($row['created_at']) || empty($row['ultima_atualizacao'])) {
                continue;
            }

            try {
                $dataCriacao = new \DateTimeImmutable($row['created_at']);
                $dataUltimaAtualizacao = new \DateTimeImmutable($row['ultima_atualizacao']);
            } catch (\Exception $e) {
                continue;
            }

            $diasDesdeCriacao = $dataCriacao->diff($now)->days;
            $diasDesdeUltimaAtualizacao = $dataUltimaAtualizacao->diff($now)->days;

            $statusId = (int)($row['status_atual_id'] ?? 0);
            $osId = (int)($row['id'] ?? 0);
            $statusNome = $row['status_nome'] ?? '';
            $statusPagamento = $row['status_pagamento'] ?? 'pendente';
            $statusEntrega = $row['status_entrega'] ?? 'nao_entregue';
            $clienteNome = $row['cliente_nome'] ?? '';
            $clienteTelefone = $row['cliente_telefone'] ?? '';

            if ($statusId === 5) {
                if ($statusPagamento === 'pago' && $statusEntrega === 'entregue') {
                    $diasEntrega = $diasDesdeUltimaAtualizacao;
                    if ($diasEntrega >= 7 && $diasEntrega <= 10) {
                        $primeiroNome = explode(' ', trim($clienteNome))[0] ?? '';
                        $alertas[] = [
                            'tipo' => 'pos_venda',
                            'nivel' => 'todos',
                            'prioridade' => 'media',
                            'os_id' => $osId,
                            'status_nome' => $statusNome,
                            'dias' => $diasEntrega,
                            'ultima_atualizacao' => $row['ultima_atualizacao'],
                            'cliente_nome' => $clienteNome,
                            'cliente_telefone' => $clienteTelefone,
                            'mensagem' => sprintf(
                                'Pós-venda: contatar cliente da OS #%d (%s) após %d dia(s) da entrega.',
                                $osId,
                                $primeiroNome,
                                $diasEntrega
                            )
                        ];
                    }
                    continue;
                }

                $mensagem = '';

                if ($statusPagamento !== 'pago' && $statusEntrega !== 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e está aguardando pagamento e entrega.',
                        $osId
                    );
                } elseif ($statusPagamento === 'pago' && $statusEntrega !== 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e paga. Aguardando entrega ao cliente.',
                        $osId
                    );
                } elseif ($statusPagamento !== 'pago' && $statusEntrega === 'entregue') {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada e entregue. Pagamento pendente.',
                        $osId
                    );
                } else {
                    $mensagem = sprintf(
                        'OS #%d foi finalizada. Verificar pendências.',
                        $osId
                    );
                }

                $alertas[] = [
                    'tipo' => 'os_finalizada',
                    'nivel' => 'todos',
                    'prioridade' => 'alta',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'status_pagamento' => $statusPagamento,
                    'status_entrega' => $statusEntrega,
                    'dias' => $diasDesdeUltimaAtualizacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => $mensagem
                ];

                continue;
            }

            if ($statusId === 6) {
                continue;
            }

            if ($diasDesdeUltimaAtualizacao >= $diasSemAtualizacao) {
                $alertas[] = [
                    'tipo' => 'os_sem_atualizacao',
                    'nivel' => 'tecnico',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'dias' => $diasDesdeUltimaAtualizacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => sprintf(
                        'OS #%d está sem atualização há %d dia(s). Verificar andamento.',
                        $osId,
                        $diasDesdeUltimaAtualizacao
                    )
                ];
            } elseif ($diasDesdeCriacao >= $diasAbertasAtraso) {
                $alertas[] = [
                    'tipo' => 'os_atrasada',
                    'nivel' => 'tecnico',
                    'os_id' => $osId,
                    'status_nome' => $statusNome,
                    'dias' => $diasDesdeCriacao,
                    'ultima_atualizacao' => $row['ultima_atualizacao'],
                    'mensagem' => sprintf(
                        'OS #%d está em aberto há %d dia(s).',
                        $osId,
                        $diasDesdeCriacao
                    )
                ];
            }
        }

        return $alertas;
    }
}
