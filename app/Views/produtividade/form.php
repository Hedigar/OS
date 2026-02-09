<div class="container">
    <div class="d-flex justify-between align-center mb-4 flex-wrap">
        <h1>Registrar Atividade</h1>
        <a href="<?php echo BASE_URL; ?>produtividade" class="btn btn-secondary">Voltar</a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>produtividade/salvar" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Data/Hora</label>
                        <input type="datetime-local" name="data_hora" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Atividade</label>
                        <select name="tipo" class="form-control" required>
                            <option value="">Selecione</option>
                            <option>Atendimento técnico</option>
                            <option>Visita a cliente</option>
                            <option>Criação de orçamentos</option>
                            <option>Desenvolvimento do sistema</option>
                            <option>Manutenção do sistema</option>
                            <option>Compras/fornecedores</option>
                            <option>Revisão de marketing</option>
                            <option>Análise financeira/taxas</option>
                            <option>Acompanhamento de equipe</option>
                            <option>Validação de processos</option>
                            <option>Conferência de estoque</option>
                            <option>Planejamento semanal</option>
                            <option>Planejamento mensal</option>
                            <option>Reuniões com sócios</option>
                            <option>Análise de indicadores</option>
                            <option>Melhorias de processos</option>
                            <option>Estudo/capacitação</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tempo gasto (minutos)</label>
                        <input type="number" name="tempo_minutos" class="form-control" min="1" step="1" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Descrição breve</label>
                        <input type="text" name="descricao" class="form-control" maxlength="255">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Local</label>
                        <select name="local" class="form-control">
                            <option>Presencial</option>
                            <option>Home Office</option>
                            <option>Cliente</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
</div>
