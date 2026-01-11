<?php

use App\Core\Router;

$router = new Router();

// Rotas de Autenticação
$router->get('', 'AuthController@showLogin'); // Página inicial é o login
$router->get('login', 'AuthController@showLogin');
$router->post('login', 'AuthController@login');
$router->get('logout', 'AuthController@logout');

// Rotas Protegidas (Dashboard)
$router->get('dashboard', 'DashboardController@index');
// Configurações
$router->get('configuracoes', 'ConfiguracoesController@index');
$router->get('configuracoes/os', 'ConfiguracoesController@os');
$router->post('configuracoes/salvar-impressao', 'ConfiguracoesController@salvarImpressao');
$router->get('configuracoes/produtos-servicos', 'ProdutoServicoController@index');
$router->get('configuracoes/produtos-servicos/form', 'ProdutoServicoController@form');
$router->post('configuracoes/produtos-servicos/salvar', 'ProdutoServicoController@store');
$router->post('configuracoes/produtos-servicos/atualizar', 'ProdutoServicoController@update');
$router->post('configuracoes/produtos-servicos/deletar', 'ProdutoServicoController@destroy');
	$router->post('configuracoes/salvar-porcentagem', 'ProdutoServicoController@salvarConfiguracao');
	$router->get('configuracoes/get-porcentagem-ajax', 'ProdutoServicoController@getPorcentagemAjax');
	$router->post('configuracoes/atualizar-precos-massa', 'ProdutoServicoController@atualizarPrecosGlobais');
// Despesas
$router->get('despesas', 'DespesasController@index');

// Rotas CRUD de Clientes (Exemplo)
$router->get('usuarios', 'UsuarioController@index');
$router->get('usuarios/form', 'UsuarioController@form'); // Usando 'form' para criar/editar
$router->post('usuarios/salvar', 'UsuarioController@store');
$router->post('usuarios/atualizar', 'UsuarioController@update');
$router->post('usuarios/deletar', 'UsuarioController@destroy');
$router->get('usuarios/trocar-senha', 'UsuarioController@showTrocarSenha');
$router->post('usuarios/salvar-nova-senha', 'UsuarioController@salvarNovaSenha');
$router->post('usuarios/resetar-senha', 'UsuarioController@resetarSenha');

// Rotas de Atendimento Externo
$router->get('atendimentos-externos', 'AtendimentoExternoController@index');
$router->get('atendimentos-externos/form', 'AtendimentoExternoController@form');
$router->post('atendimentos-externos/salvar', 'AtendimentoExternoController@store');
$router->post('atendimentos-externos/atualizar', 'AtendimentoExternoController@update');
$router->post('atendimentos-externos/deletar', 'AtendimentoExternoController@destroy');
$router->get('atendimentos-externos/print', 'AtendimentoExternoController@print');
$router->get('atendimentos-externos/view', 'AtendimentoExternoController@visualizar');
$router->get('atendimentos/search-items', 'AtendimentoExternoController@searchItems');
$router->post('atendimentos/saveItem', 'AtendimentoExternoController@saveItem');
$router->post('atendimentos/atualizar-item', 'AtendimentoExternoController@updateItem');
$router->post('atendimentos/remover-item', 'AtendimentoExternoController@removeItem');





// Rotas CRUD de Ordens de Serviço
$router->get('ordens', 'OrdemServicoController@index');
$router->get('ordens/form', 'OrdemServicoController@form'); // Usando 'form' para criar/editar
$router->post('ordens/salvar', 'OrdemServicoController@store');
$router->post('ordens/atualizar', 'OrdemServicoController@update');
$router->get('ordens/view', 'OrdemServicoController@showView'); // Visualizar OS
$router->get('ordens/print', 'OrdemServicoController@printOS'); // Imprimir OS
$router->get('ordens/print-receipt', 'OrdemServicoController@printReceipt'); // Impressão: Recepção (2 cópias por A4)
$router->get('ordens/print-estimate', 'OrdemServicoController@printEstimate'); // Impressão: Orçamento
$router->post('ordens/deletar', 'OrdemServicoController@destroy');
$router->get('ordens/search-client', 'OrdemServicoController@searchClient'); // Busca de cliente para Autocomplete na OS
$router->get('ordens/search-equipamentos', 'OrdemServicoController@searchEquipamentos');
$router->get('ordens/search-items', 'OrdemServicoController@searchItems');
$router->post('ordens/salvar-item', 'OrdemServicoController@saveItem');
$router->post('ordens/atualizar-item', 'OrdemServicoController@updateItem');
$router->post('ordens/remover-item', 'OrdemServicoController@removeItem');
$router->get('clientes', 'ClienteController@index');
$router->get('clientes/criar', 'ClienteController@create');
$router->post('clientes/salvar', 'ClienteController@store');
$router->get('clientes/view', 'ClienteController@showView'); // Visualizar Cliente
$router->get('clientes/editar', 'ClienteController@edit'); // Ex: clientes/editar?id=1
$router->post('clientes/atualizar', 'ClienteController@update');
$router->post('clientes/deletar', 'ClienteController@destroy'); // Ex: clientes/deletar?id=1
$router->get('clientes/search-ajax', 'ClienteController@searchAjax'); // Busca de cliente para Autocomplete
$router->get('clientes/details', 'ClienteController@getClientDetails'); // Detalhes do cliente por ID (AJAX)
$router->get('clientes/verificar-documento', 'ClienteController@verificarDocumento'); // Verificar se CPF/CNPJ já existe

return $router;
