<?php

namespace App\Controller;

use App\Entity\Passenger;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class PassengersController extends ApiController
{
    public function index(Request $request, Response $response): Response
    {
        $passengers = $this->entityManager->getRepository(Passenger::class)->findAll();

        $passengersJson = $this->serializer->serialize(
            ['passengers' => $passengers],
        );

        $response->getBody()->write($passengersJson);

        return $response->withHeader('Cache-Control', 'public, max-age=600');
    }

    public function show(Request $request, Response $response, string $reference): Response
    {
        $passenger = $this->entityManager->getRepository(Passenger::class)
            ->findOneBy(['reference' => $reference]);

        if ($passenger === null) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $jsonPassenger = $this->serializer->serialize(
            [
                'passenger' => $passenger
            ]
        );

        $response->getBody()->write($jsonPassenger);

        return $response;
    }

    public function store(Request $request, Response $response): Response
    {
        $passengerJson = $request->getBody()->getContents();

        $passenger = $this->serializer->deserialize(
            $passengerJson,
            Passenger::class
        );

        assert($passenger instanceof Passenger);

        // Set the passenger reference
        $passenger->setReference(time() . substr($passenger->getLastName(), 0, 3));

        $this->validator->validate($passenger, $request);

        $this->entityManager->persist($passenger);

        $this->entityManager->flush();

        $passengerJson = $this->serializer->serialize(
            [
                'passenger' => $passenger
            ]
        );

        $response->getBody()->write($passengerJson);

        return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    public function destroy(Request $request, Response $response, string $reference): Response
    {
        $passenger = $this->entityManager->getRepository(Passenger::class)
            ->findOneBy(['reference' => $reference]);

        if ($passenger === null) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->entityManager->remove($passenger);
        $this->entityManager->flush();

        return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }

    public function update(Request $request, Response $response, string $reference): Response 
    {
        $passenger = $this->entityManager->getRepository(Passenger::class)
            ->findOneBy(['reference' => $reference]);

        if ($passenger === null) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        // Grab the post data and map to a passenger
        $passengerJson = $request->getBody()->getContents();

        // Deserialize
        $passenger = $this->serializer->deserialize(
            data: $passengerJson,
            type: Passenger::class,
            context: [
                AbstractNormalizer::OBJECT_TO_POPULATE => $passenger,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['reference']
            ]
        );

        $this->validator->validate($passenger, $request);

        // Persist
        $this->entityManager->persist($passenger);
        $this->entityManager->flush();

        $jsonPassenger = $this->serializer->serialize(
            ['passenger' => $passenger]
        );

        // Add the passenger to the response body
        $response->getBody()->write($jsonPassenger);

        return $response->withStatus(StatusCodeInterface::STATUS_OK);
    }
}
