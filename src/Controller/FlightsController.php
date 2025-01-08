<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Flight;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

readonly class FlightsController extends ApiController
{
    public function index(Request $request, Response $response): Response
    {
        // Retrieve the flights
        $flights = $this->entityManager->getRepository(Flight::class)->findAll();

        // Serialize
        $json = $this->serializer->serialize(['flights' => $flights], 'json');

        // Return the response containing the flights
        $response->getBody()->write($json);

        return $response->withHeader('Content-Type', 'application/json');
    }
}