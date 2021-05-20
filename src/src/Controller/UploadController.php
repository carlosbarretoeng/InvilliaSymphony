<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Person;
use App\Entity\ShipOrder;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UploadController extends AbstractController
{
    public function upload(
        Request $request,
        string $uploadDirectory
    ): Response {
        try {
            $token = $request->get('token');

            $this->validateCsrfToken($token);
            $this->validateFiles($request->files->get('files'));

            $files = $request->files->get('files');

            $this->uploadFile($files, $uploadDirectory);
            return $this->makeResponse(Response::HTTP_OK, 'File(s) Uploaded');
        } catch (Exception $exception) {
            return $this->makeResponse(
                Response::HTTP_BAD_REQUEST,
                $exception->getMessage()
            );
        }
    }

    private function uploadFile(
        mixed $files,
        string $uploadDirectory
    ): void {
        foreach ($files as $file) {
            $filename = date('c') . '-' . $file->getClientOriginalName();
            $file->move($uploadDirectory, $filename);
            $process = $this->processXmlFile(
                $uploadDirectory . '/' . $filename
            );
            if ($process !== true) {
                throw new Exception(
                    $file->getClientOriginalName() . ' ~> ' . $process
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    private function validateCsrfToken(mixed $token): void
    {
        if (! $this->isCsrfTokenValid('upload', $token)) {
            throw new Exception('Operation not allowed');
        }
    }

    /**
     * @throws Exception
     */
    private function validateFiles(mixed $files): void
    {
        if (count($files) === 0) {
            throw new Exception('No file specified');
        }
    }

    private function makeResponse(int $status, string $message): Response
    {
        return new Response(
            $message,
            $status,
            ['content-type' => 'text/plain']
        );
    }

    public function processXmlFile(string $filePath): bool | string
    {
        $domCrawler = new Crawler();
        $domCrawler->addXmlContent(file_get_contents($filePath));

        $xmlRootNodeName = $this->getRootNodeName($domCrawler);

        if (is_null($xmlRootNodeName)) {
            return 'Invalid XML format.';
        }

        switch ($xmlRootNodeName) {
            case 'people':
                $this->processXmlProfilePeople($domCrawler);
                return true;
            case 'shiporders':
                $this->processXmlProfileShipOrders($domCrawler);
                return true;
            default:
                return 'XML Profile not found';
        }
    }

    private function getRootNodeName(Crawler $domCrawler): mixed
    {
        if (count($domCrawler->extract(['_name'])) === 0) {
            return null;
        }
        return $domCrawler->extract(['_name'])[0];
    }

    private function processXmlProfilePeople(Crawler $domCrawler): void
    {
        $domCrawler->filter('people person')
            ->each(function (Crawler $node): void {
                $personId = $node->filter('personid')->getNode(0)->nodeValue;
                $personName = $node->filter('personname')
                    ->getNode(0)->nodeValue;
                $phones = $node->filter('phones phone')
                    ->each(function (Crawler $phone) {
                        return $phone->text();
                    });

                $entityManager = $this->getDoctrine()->getManager();
                $hasPerson = $entityManager->getRepository(Person::class)
                    ->find($personId);

                if ($hasPerson) {
                    $hasPerson->populate(
                        $hasPerson->getId(),
                        $personName,
                        $phones
                    );
                    $entityManager->flush();
                    return;
                }
                $person = new Person();
                $person->populate(
                    (int) $personId,
                    $personName,
                    $phones
                );
                $entityManager->persist($person);
                $entityManager->flush();
            });
    }

    private function processXmlProfileShipOrders(Crawler $domCrawler): void
    {
        $domCrawler->filter('shiporders shiporder')
            ->each(function (Crawler $node): void {
                $orderId = $node->filter('orderid')
                    ->getNode(0)->nodeValue;
                $orderPersonId = $node->filter('orderperson')
                    ->getNode(0)->nodeValue;
                $shipTo = $this->processXmlProfileShipOrdersShipTo(
                    $node->filter('shipto')
                );
                $items = $this->processXmlProfileShipOrdersItems(
                    $node->filter('items')
                );

                $entityManager = $this->getDoctrine()->getManager();

                $hasShipOrder = $entityManager->getRepository(ShipOrder::class)
                    ->find($orderId);

                $orderPerson = $entityManager->getRepository(Person::class)
                    ->find($orderPersonId);

                if ($hasShipOrder) {
                    $hasShipOrder->populateShipOrder(
                        $orderPerson,
                        $shipTo,
                        $items
                    );
                    $entityManager->flush();
                    return;
                }

                $shipOrder = new ShipOrder();
                $shipOrder->populateShipOrder($orderPerson, $shipTo, $items);
                $entityManager->persist($shipOrder);
                $entityManager->flush();
            });
    }

    /**
     * @return array<string> array
     */
    private function processXmlProfileShipOrdersShipTo(Crawler $shipTo): array
    {
        return [
            'name' => $shipTo->filter('shipto name')->text(),
            'address' => $shipTo->filter('shipto address')->text(),
            'city' => $shipTo->filter('shipto city')->text(),
            'country' => $shipTo->filter('shipto country')->text(),
        ];
    }

    /**
     * @return array<string,string> array
     */
    private function processXmlProfileShipOrdersItems(Crawler $items): array
    {
        return $items->filter('item')->each(function (Crawler $node): array {
            return [
                'title' => $node->filter('title')->text(),
                'note' => $node->filter('note')->text(),
                'quantity' => (int) $node->filter('quantity')->text(),
                'price' => (float) $node->filter('price')->text(),
            ];
        });
    }
}
