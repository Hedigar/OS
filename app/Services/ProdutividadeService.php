<?php

namespace App\Services;

use App\Models\AtividadePessoal;
use App\Models\Log;
use App\Models\AtendimentoExterno;
use App\Core\Auth;

class ProdutividadeService
{
    private AtividadePessoal $atividadeModel;
    private Log $logModel;
    private AtendimentoExterno $atendimentoModel;

    private const LOG_DURATION_MIN = 10;

    public function __construct()
    {
        $this->atividadeModel = new AtividadePessoal();
        $this->logModel = new Log();
        $this->atendimentoModel = new AtendimentoExterno();
    }

    public function categorizar(string $tipo): string
    {
        $tipoLower = mb_strtolower($tipo);
        $op = ['atendimento técnico','visita a cliente','orçamento','desenvolvimento do sistema','manutenção do sistema','compras','fornecedor','compras/fornecedores'];
        $sup = ['revisão de marketing','análise financeira','acompanhamento de equipe','validação de processos','conferência de estoque'];
        $est = ['planejamento','reunião','indicadores','melhoria de processos','estudo','capacitação','planejamento semanal','planejamento mensal'];
        if ($this->inList($tipoLower, $op)) return 'operacional';
        if ($this->inList($tipoLower, $sup)) return 'supervisao';
        if ($this->inList($tipoLower, $est)) return 'estrategico';
        return 'operacional';
    }

    private function inList(string $tipoLower, array $list): bool
    {
        foreach ($list as $item) {
            if (str_contains($tipoLower, mb_strtolower($item))) return true;
        }
        return false;
    }

    public function sincronizarPeriodo(int $usuarioId, string $inicio, string $fim): void
    {
        $this->sincronizarLogs($usuarioId, $inicio, $fim);
        $this->sincronizarAtendimentosExternos($usuarioId, $inicio, $fim);
    }

    private function sincronizarLogs(int $usuarioId, string $inicio, string $fim): void
    {
        $logs = $this->logModel->findFiltered($usuarioId, substr($inicio, 0, 10), substr($fim, 0, 10), null, 500, 0);
        foreach ($logs as $l) {
            $acao = $l['acao'] ?? '';
            $ref = $l['referencia'] ?? null;
            $dh = $l['created_at'] ?? date('Y-m-d H:i:s');
            $tipo = $this->mapTipoPorLog($acao);
            $categoria = $this->categorizar($tipo);
            $local = $this->mapLocalPorLog($acao);
            $this->atividadeModel->upsertByOrigem($usuarioId, 'log', (int)$l['id'], [
                'data_hora' => $dh,
                'tipo' => $tipo,
                'descricao' => $ref,
                'tempo_minutos' => self::LOG_DURATION_MIN,
                'local' => $local,
                'categoria' => $categoria
            ]);
        }
    }

    private function mapTipoPorLog(string $acao): string
    {
        $a = mb_strtolower($acao);
        if (str_contains($a, 'atualizou ordem de serviço') || str_contains($a, 'criou nova ordem de serviço')) return 'Atendimento técnico';
        if (str_contains($a, 'excluiu atendimento externo') || str_contains($a, 'atendimento externo')) return 'Visita a cliente';
        if (str_contains($a, 'cadastrou novo produto/serviço') || str_contains($a, 'atualizou produto/serviço')) return 'Conferência de estoque';
        if (str_contains($a, 'realizou login') || str_contains($a, 'realizou logout')) return 'Acompanhamento de equipe';
        if (str_contains($a, 'criou despesa')) return 'Análise financeira';
        if (str_contains($a, 'salvar-config') || str_contains($a, 'configurações')) return 'Melhorias de processos';
        if (str_contains($a, 'orçamento')) return 'Criação de orçamentos';
        return 'Desenvolvimento do sistema';
    }

    private function mapLocalPorLog(string $acao): string
    {
        $a = mb_strtolower($acao);
        if (str_contains($a, 'atendimento externo') || str_contains($a, 'visita')) return 'Cliente';
        return 'Presencial';
    }

    private function sincronizarAtendimentosExternos(int $usuarioId, string $inicio, string $fim): void
    {
        $sql = "SELECT * FROM atendimentos_externos 
                WHERE ativo = 1 
                  AND usuario_id = :usuario_id
                  AND created_at BETWEEN :inicio AND :fim";
        $stmt = $this->atividadeModel->getConnection()->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId, 'inicio' => $inicio, 'fim' => $fim]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        foreach ($rows as $r) {
            $tempo = $this->parseTempoTotal($r['tempo_total'] ?? null);
            $dh = $r['created_at'] ?? date('Y-m-d H:i:s');
            $desc = $r['descricao_problema'] ?? null;
            $this->atividadeModel->upsertByOrigem($usuarioId, 'atendimento', (int)$r['id'], [
                'data_hora' => $dh,
                'tipo' => 'Visita a cliente',
                'descricao' => $desc,
                'tempo_minutos' => $tempo > 0 ? $tempo : 60,
                'local' => 'Cliente',
                'categoria' => 'operacional'
            ]);
        }
    }

    private function parseTempoTotal(?string $tempo): int
    {
        if (!$tempo) return 0;
        if (preg_match('/^(\d+):(\d{2})$/', $tempo, $m)) {
            return ((int)$m[1]) * 60 + (int)$m[2];
        }
        return 0;
    }

    public function dashboardDia(int $usuarioId, string $data): array
    {
        $inicio = $data . ' 00:00:00';
        $fim = $data . ' 23:59:59';
        $this->sincronizarPeriodo($usuarioId, $inicio, $fim);
        $atividades = $this->atividadeModel->listarPorPeriodo($usuarioId, $inicio, $fim);
        $somas = $this->atividadeModel->sumPorCategoria($usuarioId, $inicio, $fim);
        $total = array_sum($somas);
        return [
            'atividades' => $atividades,
            'somas' => $somas,
            'total' => $total
        ];
    }

    public function dashboardSemana(int $usuarioId, string $dataBase): array
    {
        $base = new \DateTimeImmutable($dataBase);
        $inicio = $base->sub(new \DateInterval('P6D'))->format('Y-m-d') . ' 00:00:00';
        $fim = $base->format('Y-m-d') . ' 23:59:59';
        $this->sincronizarPeriodo($usuarioId, $inicio, $fim);
        $atividades = $this->atividadeModel->listarPorPeriodo($usuarioId, $inicio, $fim);
        $somas = $this->atividadeModel->sumPorCategoria($usuarioId, $inicio, $fim);
        $dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $base->sub(new \DateInterval("P{$i}D"))->format('Y-m-d');
            $dias[$d] = ['operacional' => 0, 'supervisao' => 0, 'estrategico' => 0];
        }
        foreach ($atividades as $a) {
            $d = substr($a['data_hora'], 0, 10);
            $cat = $a['categoria'];
            $dias[$d][$cat] += (int)$a['tempo_minutos'];
        }
        $alertas = $this->gerarAlertasSemana($somas, $atividades, $inicio, $fim);
        return [
            'dias' => $dias,
            'somas' => $somas,
            'alertas' => $alertas
        ];
    }

    private function gerarAlertasSemana(array $somas, array $atividades, string $inicio, string $fim): array
    {
        $total = array_sum($somas);
        $alerts = [];
        $temEstrategico = $somas['estrategico'] > 0;
        if (!$temEstrategico) {
            $alerts[] = "⚠️ Faz 7 dias que não há registro de atividades estratégicas";
        }
        if ($total > 0) {
            $percOperacional = round(($somas['operacional'] / $total) * 100);
            if ($percOperacional >= 80) {
                $alerts[] = "⚠️ " . $percOperacional . "% do tempo na última semana foi operacional";
            } else {
                $alerts[] = "✅ Boa distribuição de tempo esta semana";
            }
        }
        return $alerts;
    }

    public function dashboardMes(int $usuarioId, string $mes): array
    {
        $inicio = $mes . '-01 00:00:00';
        $fim = (new \DateTimeImmutable($inicio))->modify('last day of this month')->format('Y-m-d') . ' 23:59:59';
        $this->sincronizarPeriodo($usuarioId, $inicio, $fim);
        $atividades = $this->atividadeModel->listarPorPeriodo($usuarioId, $inicio, $fim);
        $somas = $this->atividadeModel->sumPorCategoria($usuarioId, $inicio, $fim);
        $dias = [];
        $dt = new \DateTimeImmutable($inicio);
        $last = new \DateTimeImmutable($fim);
        while ($dt <= $last) {
            $d = $dt->format('Y-m-d');
            $dias[$d] = ['operacional' => 0, 'supervisao' => 0, 'estrategico' => 0];
            $dt = $dt->add(new \DateInterval('P1D'));
        }
        foreach ($atividades as $a) {
            $d = substr($a['data_hora'], 0, 10);
            $cat = $a['categoria'];
            if (isset($dias[$d])) {
                $dias[$d][$cat] += (int)$a['tempo_minutos'];
            }
        }
        return [
            'dias' => $dias,
            'somas' => $somas,
            'atividades' => $atividades
        ];
    }
}

