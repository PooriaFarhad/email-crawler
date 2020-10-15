<?php

namespace App\Tests\API;

use App\CrawlerService\RequestManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestTest extends WebTestCase
{
    private $kernelBrowser;

    protected function setUp()
    {
        $this->kernelBrowser = static::createClient();
    }

    public function testRequestsIndex()
    {
        $this->kernelBrowser->request('GET', '/api/requests');
        $this->assertEquals(200, $this->kernelBrowser->getResponse()->getStatusCode());
    }

    public function testRequestShow()
    {
        $requestManager = self::$container->get(RequestManager::class);
        $requestManager->createNewRequest('http://pooria.com');
        $this->kernelBrowser->request('GET', '/api/requests');
        $this->assertEquals(200, $this->kernelBrowser->getResponse()->getStatusCode());
    }

    public function testRequestStore()
    {
        $this->kernelBrowser->request('POST', '/api/requests', [], [], [], json_encode(['url' => 'http://pooria.com']));
        $this->assertEquals(200, $this->kernelBrowser->getResponse()->getStatusCode());
    }

    public function testGetEmails()
    {
        $requestManager = self::$container->get(RequestManager::class);
        $request = $requestManager->createNewRequest('http://pooria.com');
        $this->kernelBrowser->request('GET', '/api/requests/' . $request->getId() . '/emails');
        $this->assertEquals(200, $this->kernelBrowser->getResponse()->getStatusCode());
    }
}