<?php


declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middleware\JwtMiddleware;
use App\Middleware\JwtHelper;
use Slim\App;

return function (App $app) {
    // Connexion : renvoie un token
    $app->post('/login', function (Request $request, Response $response) {
        $params = (array)$request->getParsedBody();
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';

        if ($username === 'SaintMichel' && $password === 'ITcampus') {
            $token = JwtHelper::generateToken(['id' => 1, 'username' => $username]);
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    });

    // Route protégée
    $app->get('/protected', function (Request $request, Response $response) {
        $user = $request->getAttribute('user');
        $response->getBody()->write(json_encode(['message' => 'Hello, ' . $user->username]));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new JwtMiddleware());
};