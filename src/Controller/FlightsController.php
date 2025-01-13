<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\FlightDTO;
use App\Entity\Flight;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

readonly class FlightsController extends ApiController
{
    public function index(Request $request, Response $response): Response
    {
        // Retrieve the flights
        $flights = $this->entityManager->getRepository(Flight::class)->findAll();

        // Serialize
        $responseContent = $this->serializer->serialize(
            ['flights' => $flights], 
            $request->getAttribute('Content-Type')->format()
        );

        // Return the response containing the flights
        $response->getBody()->write($responseContent);

        return $response->withHeader('Cache-Control', 'public, max-age=600');
    }

    public function show(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy([
            'number' => $number
        ]);

        $jsonFlight = $this->serializer->serialize(
            ['flight' => $flight],
            $request->getAttribute('Content-Type')->format()
        );

        $response->getBody()->write($jsonFlight);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $body =json_encode($request->getParsedBody());

        $flight = $this->serializer->deserialize(
            $body, 
            Flight::class, 
            $request->getAttribute('Content-Type')->format()
        );

        $this->entityManager->persist($flight);
        
        $this->entityManager->flush($flight);

        $flightResponse = $this->serializer->serialize(['flight' => $flight], $request->getAttribute('Content-Type')->format());

        $response->getBody()->write($flightResponse);

        return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}