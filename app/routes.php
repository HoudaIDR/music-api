<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// =====================================================================
// V1 : Slim 4 + CRUD + méthode spécifique (SQL directement dans les routes)
// =====================================================================
return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    // Petite fonction pour répondre en JSON avec un code HTTP
    $json = function (Response $response, $data, int $status = 200): Response {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    };

    // READ : tous les artistes (200)
    $app->get('/GetAllArtist', function (Request $request, Response $response) use ($json) {
        $db = $this->get(PDO::class);
        $data = $db->query("SELECT * FROM `artists`")->fetchAll(PDO::FETCH_ASSOC);
        return $json($response, $data);
    });

    // READ : un artiste (200 / 404)
    $app->get('/getArtistById/{id}', function (Request $request, Response $response, array $args) use ($json) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists` WHERE idArtist = :id");
        $sth->bindParam(':id', $args['id'], PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            return $json($response, ['error' => 'Artiste introuvable'], 404);
        }
        return $json($response, $data);
    });

    // CREATE : ajouter un artiste (201 / 400)
    $app->post('/addArtist', function (Request $request, Response $response) use ($json) {
        $input = (array) $request->getParsedBody();
        if (empty($input['Name'])) {
            return $json($response, ['error' => 'Le champ Name est obligatoire'], 400);
        }
        $db = $this->get(PDO::class);
        $sth = $db->prepare("INSERT INTO `artists` (`Name`, `Annee`, `Description`) VALUES (:name, :annee, :description)");
        $sth->execute([
            'name'        => $input['Name'],
            'annee'       => $input['Annee'] ?? null,
            'description' => $input['Description'] ?? '',
        ]);
        return $json($response, ['message' => 'Artiste ajouté avec succès', 'idArtist' => $db->lastInsertId()], 201);
    });

    // UPDATE : modifier un artiste (200 / 404)
    $app->put('/updateArtist/{id}', function (Request $request, Response $response, array $args) use ($json) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists` WHERE idArtist = :id");
        $sth->execute(['id' => (int) $args['id']]);
        $artist = $sth->fetch(PDO::FETCH_ASSOC);
        if ($artist === false) {
            return $json($response, ['error' => 'Artiste introuvable'], 404);
        }
        $input = (array) $request->getParsedBody();
        // champ absent du Body → on garde l'ancienne valeur
        $sth = $db->prepare("UPDATE `artists` SET `Name` = :name, `Annee` = :annee, `Description` = :description WHERE idArtist = :id");
        $sth->execute([
            'name'        => $input['Name'] ?? $artist['Name'],
            'annee'       => $input['Annee'] ?? $artist['Annee'],
            'description' => $input['Description'] ?? $artist['Description'],
            'id'          => (int) $args['id'],
        ]);
        return $json($response, ['message' => 'Artiste modifié']);
    });

    // DELETE : supprimer un artiste (204 / 404)
    $app->delete('/deleteArtist/{id}', function (Request $request, Response $response, array $args) use ($json) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("DELETE FROM `artists` WHERE idArtist = :id");
        $sth->execute(['id' => (int) $args['id']]);
        if ($sth->rowCount() === 0) {
            return $json($response, ['error' => 'Artiste introuvable'], 404);
        }
        return $response->withStatus(204);   // 204 No Content
    });

    // MÉTHODE SPÉCIFIQUE : les artistes d'une année donnée
    $app->get('/getArtistsByYear/{annee}', function (Request $request, Response $response, array $args) use ($json) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists` WHERE Annee = :annee");
        $sth->execute(['annee' => $args['annee']]);
        return $json($response, $sth->fetchAll(PDO::FETCH_ASSOC));
    });
};