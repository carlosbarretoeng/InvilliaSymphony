<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $maxFileSize = $this->humanReadableToBytes(
            ini_get('post_max_size')
        );

        if ($maxFileSize === 0) {
            $message = 'Upload it\'s blocked. The maximum ' .
                'file size is set to an invalid value.';
            return $this->render('error.html.twig', [
                'message' => $message,
            ]);
        }

        return $this->render('base.html.twig', [
            'max_file_size' => $maxFileSize,
        ]);
    }
    private function humanReadableToBytes(string $value): int
    {
        preg_match_all(
            '/([0-9]{1,})([bBkKmMgGtTeEyY]{1,})/',
            $value,
            $groups,
            PREG_PATTERN_ORDER
        );
        if (count($groups[1]) === 0) {
            return 0;
        }
        $number = $groups[1][0];
        $multiply = array_search(
            strtoupper($groups[2][0][0]),
            ['B', 'K', 'M', 'G']
        );
        return $number * pow(1024, $multiply);
    }
}
