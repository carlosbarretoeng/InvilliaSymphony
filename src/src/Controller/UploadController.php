<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\ShipOrder;
use DOMNode;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;

class UploadController extends AbstractController
{

    public function upload(Request $request, string $uploadDirectory, FileUploader $fileUploader, LoggerInterface $logger): Response
    {
        try {
            $token = $request->get("token");

            if (!$this->isCsrfTokenValid('upload', $token)) {
                $logger->info('CSRF failure');
                return new Response("Operation not allowed", Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain']);
            }

            $files = $request->files->get('files');
            if (empty($files)) {
                return new Response("No file specified", Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
            }

            foreach ($files as $file) {
                $filename = date("c") . "-" . $file->getClientOriginalName();
                $fileUploader->upload($uploadDirectory, $file, $filename);
                $process = $this->processXmlFile($uploadDirectory . "/" . $filename);
                if ($process !== true) {
                    throw new Exception($file->getClientOriginalName() . " ~> " . $process);
                }
            }

            return new Response("File(s) Uploaded", Response::HTTP_OK, ['content-type' => 'text/plain']);
        } catch (Exception $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain']);
        }
    }

    private function processXmlFile(string $filePath): bool|string
    {
        $domCrawler = new Crawler();
        $domCrawler->addXmlContent(file_get_contents($filePath));

        $xmlRootNodeName = $this->getRootNodeName($domCrawler);

        if (is_null($xmlRootNodeName)) return "Invalid XML format.";

        switch ($xmlRootNodeName) {
            case "people":
                $this->processXmlProfilePeople($domCrawler);
                return true;
            case "shiporders":
                $this->processXmlProfileShipOrders($domCrawler);
                return true;
            default:
                return "XML Profile not found";
        }
    }

    private function getRootNodeName(Crawler $domCrawler): mixed
    {
        return empty($domCrawler->extract(['_name'])) ? null : $domCrawler->extract(['_name'])[0];
    }

    private function processXmlProfilePeople(Crawler $domCrawler)
    {
        $domCrawler->filter('people person')->each(function (Crawler $node) {
            $personId = $node->filter('personid')->getNode(0)->nodeValue;
            $personName = $node->filter('personname')->getNode(0)->nodeValue;
            $phones = $node->filter('phones phone')->each(function (Crawler $phone) {
                return $phone->text();
            });

            $entityManager = $this->getDoctrine()->getManager();
            $hasPerson = $entityManager->getRepository(Person::class)->find($personId);

            if (!$hasPerson) {
                $person = new Person();
                $person->setId($personId);
                $person->setName($personName);
                $person->setPhones($phones);

                $entityManager->persist($person);
            } else {
                $hasPerson->setName($personName);
                $hasPerson->setPhones($phones);
            }

            $entityManager->flush();
        });
    }

    private function processXmlProfileShipOrders(Crawler $domCrawler)
    {
        $domCrawler->filter('shiporders shiporder')->each(function (Crawler $node) {
            $orderId = $node->filter('orderid')->getNode(0)->nodeValue;
            $orderPersonId = $node->filter('orderperson')->getNode(0)->nodeValue;
            $shipTo = $this->processXmlProfileShipOrdersShipTo($node->filter('shipto'));
            $items = $this->processXmlProfileShipOrdersItems($node->filter('items'));

            $entityManager = $this->getDoctrine()->getManager();

            $hasShipOrder = $entityManager->getRepository(ShipOrder::class)->find($orderId);

            $orderPerson = $entityManager->getRepository(Person::class)->find($orderPersonId);

            if (!$hasShipOrder) {
                $shipOrder = new ShipOrder();

                $shipOrder->setOrderperson($orderPerson);
                $shipOrder->setShipto($shipTo);
                $shipOrder->setItems($items);

                $entityManager->persist($shipOrder);
            } else {
                $hasShipOrder->setOrderperson($orderPerson);
                $hasShipOrder->setShipto($shipTo);
                $hasShipOrder->setItems($items);
            }

            $entityManager->flush();
        });
    }

    private function processXmlProfileShipOrdersShipTo(Crawler $shipTo)
    {
        return [
            "name" => $shipTo->filter('shipto name')->text(),
            "address" => $shipTo->filter('shipto address')->text(),
            "city" => $shipTo->filter('shipto city')->text(),
            "country" => $shipTo->filter('shipto country')->text()
        ];
    }

    private function processXmlProfileShipOrdersItems(Crawler $items)
    {
        return $items->filter('item')->each(function (Crawler $node) {
            return [
                "title" => $node->filter('title')->text(),
                "note" => $node->filter('note')->text(),
                "quantity" => $node->filter('quantity')->text(),
                "price" => $node->filter('price')->text()
            ];
        });
    }
}