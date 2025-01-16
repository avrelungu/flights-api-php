<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Flight;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class FlightsController extends ApiController
{
    public function index(Request $request, Response $response): Response
    {
        // Retrieve the flights
        $flights = $this->entityManager->getRepository(Flight::class)->findAll();

        // Serialize
        $responseContent = $this->serializer->serialize(
            ['flights' => $flights]
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
        );

        $response->getBody()->write($jsonFlight);

        return $response;
    }

    public function store(Request $request, Response $response): Response
    {
        $flightJson = json_encode($request->getParsedBody());

        $flight = $this->serializer->deserialize(
            $flightJson, 
            Flight::class
        );

        $this->validator->validate($flight, $request, [Flight::CREATE_GROUP]);

        $this->entityManager->persist($flight);
        
        $this->entityManager->flush();

        $flightResponse = $this->serializer->serialize(['flight' => $flight], $request->getAttribute('Content-Type')->format());

        $response->getBody()->write($flightResponse);

        return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    public function destroy(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy([
            'number' => $number
        ]);

        if (!$flight) {
            throw new HttpNotFoundException($request, 'Flight not found.');
        }

        $this->entityManager->remove($flight);

        $this->entityManager->flush();

        return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }

    public function update(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy([
            'number' => $number
        ]);

        if (!$flight) {
            throw new HttpNotFoundException($request, 'Flight not found!');
        }

        $flightJson = $request->getBody()->getContents();

        $flight = $this->serializer->deserialize(
            data: $flightJson,
            type: Flight::class,
            context: [
                AbstractNormalizer::OBJECT_TO_POPULATE => $flight,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['number']
            ]
        );

        $this->validator->validate($flight, $request, [Flight::UPDATE_GROUP]);

        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        $flightJson = $this->serializer->serialize(
            ['flight' => $flight],
        );

        $response->getBody()->write($flightJson);

        return $response;
    }
}