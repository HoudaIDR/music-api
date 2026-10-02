<?php

declare(strict_types=1);

use App\Entity\Artist;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

// Option : Doctrine (ORM)
return function (App $app) {
    $app->get('/orm/artists', function (Request $request, Response $response) {
        $artists = $this->get(EntityManager::class)->getRepository(Artist::class)->findAll();
        $data = array_map(fn (Artist $a) => $a->toArray(), $artists);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
};