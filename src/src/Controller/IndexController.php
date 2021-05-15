<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @param string $value
     * @return int
     */
    private function humanReadableToBytes(string $value): int
    {
        preg_match_all("/([0-9]{1,})([bBkKmMgGtTeEyY]{1,})/", $value, $groups, PREG_PATTERN_ORDER);
        $number = $groups[1][0];
        $multiply = array_search(strtoupper($groups[2][0][0]), array('B', 'K', 'M', 'G'));
        return $number * pow(1024, $multiply);
    }

    public function index(): Response
    {
        return $this->render('base.html.twig', [
            "max_file_size" => $this->humanReadableToBytes(/*ini_get('post_max_size')*/ "8M")
        ]);
    }

    public function upload(): Response
    {
        sleep(3);

        $response = new Response();
        $response->setContent(json_encode([
            //'success' => false,
            //'error' => 'Something wrong it\'s realy not right'
        ]));
        $response->headers->set('Content-Type', 'application/json');
        //$response->setStatusCode(500);
        return $response;
    }
}