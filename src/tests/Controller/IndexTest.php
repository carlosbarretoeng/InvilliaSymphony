<?php

namespace App\Tests\Controller;

use App\Controller\IndexController;
use App\Controller\UploadController;
use App\Tests\TestUtils;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class IndexTest extends WebTestCase
{
    use TestUtils;
    public function testHumanReadableToBytes(): void
    {
        $object = new IndexController();
        $methodName = 'humanReadableToBytes';

        $values = ['-1', '0', '128', '61k', '22K', '15m', '8M', '3G','7G'];
        $expects = [0, 0, 0, 62464, 22528, 15728640, 8388608, 3221225472, 7516192768];

        for($i = 0; $i < count($values); $i++){
            $response = $this->invokeMethod($object, $methodName, [$values[$i]]);
            $this->assertIsNumeric($response);
            $this->assertEquals($expects[$i], $response, "{$values[$i]} ~> {$expects[$i]}");
        }
    }

    public function testUploadPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label', 'Choose a file');
    }

    public function testDocumentationPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label', 'Choose a file');
    }
}
