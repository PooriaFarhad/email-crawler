<?php

namespace App\CrawlerService;

use App\Entity\Request;
use App\Entity\Url;
use App\Lib\UrlHelper;
use Doctrine\ORM\EntityManagerInterface;

class Processor
{
    const MAX_URLS_PER_REQUEST = 100;

    private $lookupService;
    private $requestRepo;
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
        while ($requestUrls = $this->urlRepo->findNotCrawledUrlsByRequest($this->request->getId())) {
            foreach ($requestUrls as $requestUrl) {
                $this->requestManager->setRequestAsProcessingIfNeeded($this->request);
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
        $foundUrls = [];
        if (count($previousUrls) < self::MAX_URLS_PER_REQUEST) {
            $foundUrls = $this->lookupService->urlLookup($this->request->getHost(), $pageContent);
        }
        $newUrlsToCrawl = array_diff($foundUrls, array_column($previousUrls, 'url'));
        $this->requestManager->persistCrawledUrl($url, $newUrlsToCrawl, $foundEmails);
    }
}