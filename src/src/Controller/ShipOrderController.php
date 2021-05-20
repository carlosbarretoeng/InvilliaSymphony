<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ShipOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ShipOrderController extends AbstractController
{
    public function getAll(): Response
    {
        $jsonEncoder = new JsonEncoder();
        $objectNormalizer = new ObjectNormalizer();

        $serializer = new Serializer([$objectNormalizer], [$jsonEncoder]);
        $entityManager = $this->getDoctrine()->getManager();

        $repository = $entityManager->getRepository(ShipOrder::class)
            ->findAll();

        $responseJson = $serializer->serialize($repository, 'json');
        $responseJson = str_replace(
            ',"__initializer__":null,"__cloner__":null,"__isInitialized__":true',
            '',
            $responseJson,
            $num
        );

        return $this->createResponse($responseJson);
    }

    public function getOne(int $id): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $entityManager = $this->getDoctrine()->getManager();

        $repository = $entityManager->getRepository(ShipOrder::class)
            ->find($id);

        $response = new Response();
        $response->setContent($serializer->serialize($repository, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function createResponse(string $responseJson): Response
    {
        $response = new Response();
        $response->setContent($responseJson);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
