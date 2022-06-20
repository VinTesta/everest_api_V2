<?php
declare(strict_types=1);

use App\Application\Actions\Usuario\LoginUsuario;
use App\Application\Actions\Evento\ListaEvento;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Usuario\AlterarUsuario;
use App\Application\Actions\Usuario\CadastraUsuario;
use App\Application\Actions\Evento\AdicionarEvento;
use App\Application\Actions\Evento\AlterarEvento;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });
    
    $app->post('/logar', LoginUsuario::class);

    $app->post('/evento/buscaEventos', ListaEvento::class);

    $app->post('/cadastrarUsuario', CadastraUsuario::class);

    $app->post('/usuario/alterarUsuario', AlterarUsuario::class);

    $app->post('/evento/adicionaEvento', AdicionarEvento::class);
    
    $app->post('/evento/alterarEvento', AlterarEvento::class);
};

