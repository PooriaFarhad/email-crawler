<?php

namespace App\CrawlerService;

use App\Entity\Request;
use App\Entity\Url;
use App\Lib\UrlHelper;
use App\Repository\RequestRepository;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;

class Processor
{
    const MAX_URLS_PER_REQUEST = 100;

    private $lookupService;
    /** @var RequestRepository */
    private $requestRepo;
    /** @var UrlRepository */
    private $urlRepo;
    private $requestManager;

    /** @var Request */
    private $request;

    public function __construct(WebLookupInterface $lookupService, RequestManager $requestManager, EntityManagerInterface $entityManager)
    {
        $this->lookupService = $lookupService;
        $this->requestManager = $requestManager;
        $this->urlRepo = $entityManager->getRepository(Url::class);
        $this->requestRepo = $entityManager->getRepository(Request::class);
    }

    public function execute()
    {
        $this->request = $this->requestRepo->findPendingRequest();
        if (!$this->request) {
            return;
        }
        $this->requestManager->setRequestAsProcessingIfNeeded($this->request);
        while ($requestUrls = $this->urlRepo->findNotCrawledUrlsByRequest($this->request->getId())) {
            foreach ($requestUrls as $requestUrl) {
                $this->crawlWebPage($requestUrl);
            }
        }
        $this->requestManager->setRequestAsProcessed($this->request);
    }

    private function crawlWebPage(Url $url)
    {
        $pageContent = $this->lookupService->fetchContent(UrlHelper::getCompleteUrl($url->getRequest()->getHost(), $url->getUrl()));
        $foundEmails = $this->lookupService->emailLookup($pageContent);
        $previousUrls = $this->urlRepo->findUrlsByRequest($this->request->getId());
        $newUrlsToCrawl = [];
        if (count($previousUrls) < self::MAX_URLS_PER_REQUEST) {
            $foundUrls = $this->lookupService->urlLookup($this->request->getHost(), $pageContent);
            $newUrlsToCrawl = array_diff(array_slice($foundUrls, 0, self::MAX_URLS_PER_REQUEST), array_column($previousUrls, 'url'));
        }
        $this->requestManager->persistCrawledUrl($url, $newUrlsToCrawl, $foundEmails);
    }
}