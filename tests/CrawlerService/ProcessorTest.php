<?php

namespace App\Tests\CrawlerService;

use App\CrawlerService\WebLookup;
use App\CrawlerService\Processor;
use App\CrawlerService\RequestManager;
use App\CrawlerService\WebLookupInterface;
use App\Entity\Email;
use App\Entity\Request;
use App\Enum\EnumStatus;
use App\Lib\UrlHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcessorTest extends KernelTestCase
{
    /** @var EntityManager */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testCrawlTwoPages()
    {
        /** @var RequestManager $requestManager */
        $requestManager = self::$container->get(RequestManager::class);
        $requestManager->createNewRequest('http://example.com/2.html');
        $requests = $this->entityManager->getRepository(Request::class)->findAll();
        $this->assertCount(1, $requests);
        $this->assertEquals(EnumStatus::NEW, reset($requests)->getStatus());

        $domCrawlerLookupMock = $this->createPartialMock(WebLookup::class, ['fetchContent']);
        $domCrawlerLookupMock->method('fetchContent')->will($this->returnValueMap(
            [
                ['http://example.com//2.html', $this->getPageTwo()],
                ['http://example.com//1.html', $this->getPageOne()]
            ]
        ));
        /** @var WebLookupInterface $domCrawlerLookupMock */
        $processor = new Processor($domCrawlerLookupMock, $requestManager, $this->entityManager);
        $processor->execute();
        $this->assertProcessingResults();
    }

    private function assertProcessingResults()
    {
        /** @var Email[] $emails */
        $emails = $this->entityManager->getRepository(Email::class)->findAll();
        $this->assertCount(2, $emails);
        $expectedFoundEmails = [
            'mail@example.com' => 'http://example.com//1.html',
            'support@example.com' => 'http://example.com//2.html'
        ];
        foreach ($emails as $email) {
            $this->assertArrayHasKey($email->getEmail(), $expectedFoundEmails);
            $this->assertEquals(
                $expectedFoundEmails[$email->getEmail()],
                UrlHelper::getCompleteUrl($email->getUrl()->getRequest()->getHost(), $email->getUrl()->getUrl())
            );
        }
    }

    private function getPageTwo(): string
    {
        return '<html><a href="http://example.com/1.html">Back</a>Please contact the support by this email: support@example.com</html>';
    }

    private function getPageOne(): string
    {
        return '<html><a href="/2.html">Link</a><a href="mailto:mail@example.com">Contact us</a></html>';
    }
}