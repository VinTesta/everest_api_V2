<?php
declare(strict_types=1);

use App\Application\Actions\Usuario\LoginUsuario;
use App\Application\Actions\Evento\ListaEvento;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Application\Actions\Usuario\CadastraUsuario;

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
};

