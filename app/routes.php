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

// Rotas CRUD de Ordens de Serviço
$router->get('ordens', 'OrdemServicoController@index');
$router->get('ordens/form', 'OrdemServicoController@form'); // Usando 'form' para criar/editar
$router->post('ordens/salvar', 'OrdemServicoController@store');
$router->post('ordens/atualizar', 'OrdemServicoController@update');
$router->get('ordens/view', 'OrdemServicoController@showView'); // Visualizar OS
$router->get('ordens/print', 'OrdemServicoController@printOS'); // Imprimir OS
$router->post('ordens/deletar', 'OrdemServicoController@destroy');
$router->get('ordens/search-client', 'OrdemServicoController@searchClient'); // Busca de cliente para Autocomplete na OS
$router->get('ordens/search-equipamentos', 'OrdemServicoController@searchEquipamentos');
$router->get('clientes', 'ClienteController@index');
$router->get('clientes/criar', 'ClienteController@create');
$router->post('clientes/salvar', 'ClienteController@store');
$router->get('clientes/view', 'ClienteController@showView'); // Visualizar Cliente
$router->get('clientes/editar', 'ClienteController@edit'); // Ex: clientes/editar?id=1
$router->post('clientes/atualizar', 'ClienteController@update');
$router->post('clientes/deletar', 'ClienteController@destroy'); // Ex: clientes/deletar?id=1
$router->get('clientes/search-ajax', 'ClienteController@searchAjax'); // Busca de cliente para Autocomplete
$router->get('clientes/details', 'ClienteController@getClientDetails'); // Detalhes do cliente por ID (AJAX)

return $router;
