<div class="container-fluid">
    <style>
        .nav-pills .nav-link, .nav-tabs .nav-link {
            cursor: pointer !important;
            position: relative;
            z-index: 500;
        }
        .tab-pane {
            display: none !important;
        }
        .tab-pane.active {
            display: block !important;
        }
        .nav-pills {
            position: relative;
            z-index: 1000;
        }
        .badge-tipo {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
    <script>
        window.switchCRMTab = function(tabId, btn) {
            document.querySelectorAll('#crm-tabs .nav-link').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('#crm-tabContent .tab-pane').forEach(p => {
                p.classList.remove('active', 'show');
                p.style.display = 'none';
            });
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.add('active', 'show');
                target.style.display = 'block';
            }
        };
    </script>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0" style="color: var(--text-primary, #fff);"><i class="fas fa-comments me-2"></i>Relatório de CRM</h2>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?php echo $filtros['data_inicio'] ?? ''; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-primary);">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?php echo $filtros['data_fim'] ?? ''; ?>" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo Geral -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Interações</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo (int)($dados['resumo']['total_interacoes'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clientes Contactados</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo (int)($dados['resumo']['total_clientes_contactados'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pós-Venda</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo (int)($dados['resumo']['total_pos_venda'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Campanhas</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo (int)($dados['resumo']['total_campanhas'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Com Resposta</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo (int)($dados['resumo']['total_com_resposta'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card shadow h-100" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-body text-center">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Média Nota</div>
                    <div class="h3 mb-0 font-weight-bold" style="color: var(--text-primary, #fff);"><?php echo number_format((float)($dados['resumo']['media_nota'] ?? 0), 1, ',', '.'); ?> <i class="fas fa-star" style="color: #ffc107;"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegação por Abas -->
    <ul class="nav nav-pills mb-4" id="crm-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-resumo" type="button" role="tab" onclick="switchCRMTab('pane-resumo', this)"><i class="fas fa-chart-line me-2"></i>Por Dia</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-usuario" type="button" role="tab" onclick="switchCRMTab('pane-usuario', this)"><i class="fas fa-users me-2"></i>Por Usuário</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-detalhes" type="button" role="tab" onclick="switchCRMTab('pane-detalhes', this)"><i class="fas fa-list me-2"></i>Detalhes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-posvenda" type="button" role="tab" onclick="switchCRMTab('pane-posvenda', this)"><i class="fas fa-handshake me-2"></i>Pós-Venda OS</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-campanhas" type="button" role="tab" onclick="switchCRMTab('pane-campanhas', this)"><i class="fas fa-bullhorn me-2"></i>Campanhas</button>
        </li>
    </ul>

    <div class="tab-content" id="crm-tabContent">
        <!-- Aba 1: Por Dia -->
        <div class="tab-pane show active" id="pane-resumo" role="tabpanel">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Interações por Dia</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">Data</th>
                                    <th class="text-center" style="color: var(--text-primary);">Total</th>
                                    <th class="text-center" style="color: var(--text-primary);">Pós-Venda</th>
                                    <th class="text-center" style="color: var(--text-primary);">Campanha</th>
                                    <th class="text-center" style="color: var(--text-primary);">Com Resposta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dados['por_dia'])): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Nenhuma interação neste período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dados['por_dia'] as $dia): ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td><?php echo date('d/m/Y', strtotime($dia['data'])); ?></td>
                                            <td class="text-center font-weight-bold"><?php echo (int)$dia['total']; ?></td>
                                            <td class="text-center"><span class="badge bg-info"><?php echo (int)$dia['pos_venda']; ?></span></td>
                                            <td class="text-center"><span class="badge bg-warning"><?php echo (int)$dia['campanha']; ?></span></td>
                                            <td class="text-center"><span class="badge bg-success"><?php echo (int)$dia['com_resposta']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 2: Por Usuário -->
        <div class="tab-pane" id="pane-usuario" role="tabpanel" style="display: none;">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Desempenho por Usuário</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">Usuário</th>
                                    <th class="text-center" style="color: var(--text-primary);">Total</th>
                                    <th class="text-center" style="color: var(--text-primary);">Pós-Venda</th>
                                    <th class="text-center" style="color: var(--text-primary);">Campanhas</th>
                                    <th class="text-center" style="color: var(--text-primary);">Com Resposta</th>
                                    <th class="text-center" style="color: var(--text-primary);">Média Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dados['por_usuario'])): ?>
                                    <tr><td colspan="6" class="text-center text-muted">Nenhum dado neste período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dados['por_usuario'] as $usuario): ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td><?php echo htmlspecialchars($usuario['usuario_nome'] ?? 'Sistema'); ?></td>
                                            <td class="text-center font-weight-bold"><?php echo (int)$usuario['total_interacoes']; ?></td>
                                            <td class="text-center"><span class="badge bg-info"><?php echo (int)$usuario['total_pos_venda']; ?></span></td>
                                            <td class="text-center"><span class="badge bg-warning"><?php echo (int)$usuario['total_campanhas']; ?></span></td>
                                            <td class="text-center"><span class="badge bg-success"><?php echo (int)$usuario['total_com_resposta']; ?></span></td>
                                            <td class="text-center"><?php echo number_format((float)($usuario['media_nota'] ?? 0), 1, ',', '.'); ?> <i class="fas fa-star" style="color: #ffc107;"></i></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 3: Detalhes -->
        <div class="tab-pane" id="pane-detalhes" role="tabpanel" style="display: none;">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Todas as Interações</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">Data</th>
                                    <th style="color: var(--text-primary);">Cliente</th>
                                    <th style="color: var(--text-primary);">Tipo</th>
                                    <th style="color: var(--text-primary);">Assunto</th>
                                    <th style="color: var(--text-primary);">Usuário</th>
                                    <th style="color: var(--text-primary);">Resposta</th>
                                    <th style="color: var(--text-primary);">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dados['detalhes'])): ?>
                                    <tr><td colspan="7" class="text-center text-muted">Nenhuma interação neste período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dados['detalhes'] as $interacao): ?>
                                        <?php 
                                            $tipoCor = match($interacao['tipo']) {
                                                'pos_venda' => 'bg-info',
                                                'campanha' => 'bg-warning',
                                                'ligacao' => 'bg-primary',
                                                default => 'bg-secondary'
                                            };
                                            $tipoLabel = match($interacao['tipo']) {
                                                'pos_venda' => 'Pós-Venda',
                                                'campanha' => 'Campanha',
                                                'ligacao' => 'Ligação',
                                                default => ucfirst($interacao['tipo'])
                                            };
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td><?php echo date('d/m/Y H:i', strtotime($interacao['created_at'])); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($interacao['cliente_nome'] ?? 'N/A'); ?></strong>
                                                <?php if ($interacao['telefone_principal']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($interacao['telefone_principal']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge <?php echo $tipoCor; ?> badge-tipo"><?php echo $tipoLabel; ?></span></td>
                                            <td>
                                                <?php echo htmlspecialchars($interacao['assunto'] ?? ''); ?>
                                                <?php if ($interacao['ordem_servico_id']): ?>
                                                    <br><small class="text-muted">OS #<?php echo (int)$interacao['ordem_servico_id']; ?></small>
                                                <?php endif; ?>
                                                <?php if ($interacao['campanha_nome']): ?>
                                                    <br><small class="text-muted">Campanha: <?php echo htmlspecialchars($interacao['campanha_nome']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($interacao['usuario_nome'] ?? 'Sistema'); ?></td>
                                            <td>
                                                <?php if ($interacao['resposta_cliente']): ?>
                                                    <span class="badge bg-success">Sim</span>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($interacao['resposta_cliente'], 0, 50)); ?>...</small>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Não</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($interacao['nota_satisfacao']): ?>
                                                    <span class="badge" style="background-color: <?php echo $interacao['nota_satisfacao'] >= 4 ? '#1cc88a' : ($interacao['nota_satisfacao'] >= 3 ? '#f6c23e' : '#e74a3b'); ?>;">
                                                        <?php echo (int)$interacao['nota_satisfacao']; ?> <i class="fas fa-star"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 4: Pós-Venda OS -->
        <div class="tab-pane" id="pane-posvenda" role="tabpanel" style="display: none;">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Pós-Venda Realizados nas OS</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">OS</th>
                                    <th style="color: var(--text-primary);">Cliente</th>
                                    <th style="color: var(--text-primary);">Data OS</th>
                                    <th style="color: var(--text-primary);">Data Pós-Venda</th>
                                    <th style="color: var(--text-primary);">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dados['pos_venda_os'])): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Nenhum pós-venda neste período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dados['pos_venda_os'] as $pv): ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td><strong>#<?php echo (int)$pv['os_id']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($pv['cliente_nome']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($pv['os_data'])); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pv['pos_venda_data'])); ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?php echo $pv['pos_venda_nota'] >= 4 ? '#1cc88a' : ($pv['pos_venda_nota'] >= 3 ? '#f6c23e' : '#e74a3b'); ?>;">
                                                    <?php echo (int)$pv['pos_venda_nota']; ?> <i class="fas fa-star"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 5: Campanhas -->
        <div class="tab-pane" id="pane-campanhas" role="tabpanel" style="display: none;">
            <div class="card shadow mb-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <div class="card-header py-3" style="background-color: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-color);">
                    <h6 class="m-0 font-weight-bold text-primary">Campanhas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" style="color: var(--text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="color: var(--text-primary);">Campanha</th>
                                    <th style="color: var(--text-primary);">Status</th>
                                    <th style="color: var(--text-primary);">Criado por</th>
                                    <th style="color: var(--text-primary);">Data Criação</th>
                                    <th class="text-center" style="color: var(--text-primary);">Total Enviados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dados['campanhas'])): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Nenhuma campanha.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($dados['campanhas'] as $campanha): ?>
                                        <?php 
                                            $statusCor = match($campanha['status']) {
                                                'ativa' => 'bg-success',
                                                'finalizada' => 'bg-secondary',
                                                default => 'bg-warning'
                                            };
                                        ?>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <td>
                                                <strong><?php echo htmlspecialchars($campanha['nome']); ?></strong>
                                                <?php if ($campanha['mensagem_padrao']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($campanha['mensagem_padrao'], 0, 100)); ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge <?php echo $statusCor; ?>"><?php echo ucfirst($campanha['status']); ?></span></td>
                                            <td><?php echo htmlspecialchars($campanha['usuario_nome'] ?? 'Sistema'); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($campanha['created_at'])); ?></td>
                                            <td class="text-center font-weight-bold"><?php echo (int)$campanha['total_enviados']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
