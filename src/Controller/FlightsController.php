<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Flight;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class FlightsController extends ApiController
{
    public function index(Request $request, Response $response): Response
    {
        $flights = $this->entityManager->getRepository(Flight::class)->findAll();

        if (!$flights) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $jsonFlights = $this->serializer->serialize(['flights' => $flights], $request->getAttribute('content-type')->format());

        $response->getBody()->write($jsonFlights);
        return $response->withHeader('Cache-Control', 'public, max-age=600');
    }

    public function show(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy(['number' => $number]);

        if (!$flight) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $jsonData = $this->serializer->serialize(['flight' => $flight], $request->getAttribute('content-type')->format());
        $response->getBody()->write($jsonData);
        
        return $response->withHeader('Cache-Control', 'public, max-age=600');
    }

    public function store(Request $request, Response $response): Response
    {
        $flightJson = $request->getBody()->getContents();
        $flightData = $this->serializer->deserialize($flightJson, Flight::class, $request->getAttribute('content-type')->format());

        $this->validator->validate($flightData, $request);

        $this->entityManager->persist($flightData);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize(['flight' => $flightData], $request->getAttribute('content-type')->format());
        $response->getBody()->write($jsonData);

        return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    public function destroy(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy(['number' => $number]);

        if (!$flight) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->entityManager->remove($flight);
        $this->entityManager->flush();

        return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }

    public function update(Request $request, Response $response, string $number): Response
    {
        $flight = $this->entityManager->getRepository(Flight::class)->findOneBy(['number' => $number]);

        if (!$flight) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $flightJson = $request->getBody()->getContents();
        $flightData = $this->serializer->deserialize(
            $flightJson,
            Flight::class,
            $request->getAttribute('content-type')->format(),
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $flight,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['number'],
            ]
        );

        $this->validator->validate($flightData, $request, [Flight::UPDATE_GROUP]);

        $this->entityManager->persist($flightData);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize(
            [
                'flight' => $flightData
            ],
            $request->getAttribute('content-type')->format()
        );

        $response->getBody()->write($jsonData);

        return $response->withStatus(StatusCodeInterface::STATUS_OK);
    }
}
