<?php

namespace App\Controller;

use App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PersonController extends AbstractController
{
    public function getAll(): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $entityManager = $this->getDoctrine()->getManager();

        $repository = $entityManager->getRepository(Person::class)->findAll();

        $response = new Response();
        $response->setContent($serializer->serialize($repository, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getOne($id): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $entityManager = $this->getDoctrine()->getManager();

        $repository = $entityManager->getRepository(Person::class)->find($id);

        $response = new Response();
        $response->setContent($serializer->serialize($repository, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
