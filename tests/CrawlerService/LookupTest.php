<?php

namespace App\Tests\CrawlerService;

use App\CrawlerService\DomCrawlerLookup;
use PHPUnit\Framework\TestCase;

class LookupTest extends TestCase
{
    private $lookUpService;

    protected function setUp()
    {
        $this->lookUpService = new DomCrawlerLookup();
    }

    public function testFindEmailInText()
    {
        $foundEmailAddresses = $this->lookUpService->emailLookup('This is my email address: pooriaf@gmail.com');
        $this->assertCount(1, $foundEmailAddresses);
        $this->assertEquals('pooriaf@gmail.com', reset($foundEmailAddresses));
    }

    public function testFindEmailInTag()
    {
        $foundEmailAddresses = $this->lookUpService->emailLookup('<a href="mailto:pooriafarhad@gmail.com"> Send email to us! </a>');
        $this->assertCount(1, $foundEmailAddresses);
        $this->assertEquals('pooriafarhad@gmail.com', reset($foundEmailAddresses));
    }

    public function testFindEmailByDifferentTopLevels()
    {
        $foundEmailAddresses = $this->lookUpService->emailLookup('pooria@domain.com, pooria@domain.net, pooria@domain.io');
        $this->assertCount(3, $foundEmailAddresses);
    }

    public function testNotFoundEmail()
    {
        $foundEmailAddresses = $this->lookUpService->emailLookup('There is no email address in this link');
        $this->assertCount(0, $foundEmailAddresses);
    }

    public function testFindLink()
    {
        $foundLinks = $this->lookUpService->urlLookup('pooria.com', '<a href="here.html">Check here!</a>');
        $this->assertCount(1, $foundLinks);
        $this->assertEquals('here.html', reset($foundLinks));
    }

    public function testNotFoundFindLink()
    {
        $foundEmailAddresses = $this->lookUpService->urlLookup('pooria.com', 'It does not have link');
        $this->assertCount(0, $foundEmailAddresses);
    }

    public function testOnlySameHostLinkFound()
    {
        $content = 'It has a same host link <a href="http://pooria.com/1.php">Same</a> and different host link <a href="http://www.different.com/2.php">different</a>';
        $foundEmailAddresses = $this->lookUpService->urlLookup('pooria.com', $content);
        $this->assertCount(1, $foundEmailAddresses);
    }
}