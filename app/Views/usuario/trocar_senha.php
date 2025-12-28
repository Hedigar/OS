<?php
// Inclui o layout principal
require_once __DIR__ . '/../layout/main.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Troca de Senha Obrigatória</h4>
                </div>
                <div class="card-body">
                    <p>Para sua segurança, você deve alterar sua senha no primeiro acesso ou após um reset.</p>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>usuarios/salvar-nova-senha" method="POST">
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required minlength="6">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Alterar Senha e Entrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé
require_once __DIR__ . '/../layout/footer.php';
?>
