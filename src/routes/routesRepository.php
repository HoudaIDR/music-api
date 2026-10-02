<?php

declare(strict_types=1);

use App\Middleware\JwtMiddleware;
use App\Repository\ArtistRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// =====================================================================
// V2 : Repository + Entity → routes REST propres /api/artists
// =====================================================================
return function (App $app) {
    $app->group('/api', function (Group $group) {

        $json = function (Response $response, $data, int $status = 200): Response {
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        };

        // GET /api/artists → liste (200)
        $group->get('/artists', function (Request $request, Response $response) use ($json) {
            return $json($response, $this->get(ArtistRepository::class)->findAll());
        });

        // GET /api/artists/year/{annee} → méthode spécifique findByYear (200)
        $group->get('/artists/year/{annee}', function (Request $request, Response $response, array $args) use ($json) {
            return $json($response, $this->get(ArtistRepository::class)->findByYear((int) $args['annee']));
        });

        // GET /api/artists/{id} → un artiste (200 / 404)
        $group->get('/artists/{id}', function (Request $request, Response $response, array $args) use ($json) {
            $artist = $this->get(ArtistRepository::class)->findById((int) $args['id']);
            return $artist
                ? $json($response, $artist)
                : $json($response, ['error' => 'Artiste introuvable'], 404);
        });

        // POST /api/artists → créer (201 / 400)
        $group->post('/artists', function (Request $request, Response $response) use ($json) {
            $data = (array) $request->getParsedBody();
            if (empty($data['Name'])) {
                return $json($response, ['error' => 'Le champ Name est obligatoire'], 400);
            }
            $id = $this->get(ArtistRepository::class)->insert($data);
            return $json($response, ['idArtist' => $id], 201);
        });

        // PUT /api/artists/{id} → modifier (200 / 404)
        $group->put('/artists/{id}', function (Request $request, Response $response, array $args) use ($json) {
            $repo = $this->get(ArtistRepository::class);
            $id = (int) $args['id'];
            if (!$repo->findById($id)) {
                return $json($response, ['error' => 'Artiste introuvable'], 404);
            }
            $repo->update($id, (array) $request->getParsedBody());
            return $json($response, $repo->findById($id));
        });

        // DELETE /api/artists/{id} → supprimer (204 / 404)
        $group->delete('/artists/{id}', function (Request $request, Response $response, array $args) use ($json) {
            if (!$this->get(ArtistRepository::class)->delete((int) $args['id'])) {
                return $json($response, ['error' => 'Artiste introuvable'], 404);
            }
            return $response->withStatus(204);
        });

    }); // ->add(new JwtMiddleware());   // toutes les routes /api sont protégées par le JWT
};