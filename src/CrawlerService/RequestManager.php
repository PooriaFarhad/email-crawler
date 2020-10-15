<?php

namespace App\CrawlerService;

use App\Entity\Email;
use App\Entity\Request;
use App\Entity\Url;
use App\Enum\EnumStatus;
use App\Lib\UrlHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RequestManager
{
    private $entityManager;
    private $urlRepo;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->urlRepo = $entityManager->getRepository(Url::class);
    }

    public function createNewRequest(string $url): Request
    {
        $urlComponents = parse_url($url);
        $request = new Request();
        $request->setHost(UrlHelper::removeHostPrefix($urlComponents['host']));
        $request->setCreatedAt(new DateTime());
        $request->setStatus(EnumStatus::NEW);
        $this->entityManager->persist($request);
        $this->createNewUrl($request, UrlHelper::getPathAndQuery($urlComponents));

        $this->entityManager->flush();

        return $request;
    }

    public function createNewUrl(Request $request, string $path, ?int $referenceId = null)
    {
        $initialUrl = new Url();
        $initialUrl->setRequest($request);
        $initialUrl->setUrl($path);
        $initialUrl->setReferenceId($referenceId);
        $this->entityManager->persist($initialUrl);
    }

    public function setRequestAsProcessingIfNeeded(Request $request)
    {
        if ($request->getStatus() == EnumStatus::PROCESSING) {
            return;
        }

        $request->setStatus(EnumStatus::PROCESSING);
        $this->entityManager->persist($request);
        $this->entityManager->flush();
    }

    public function setRequestAsProcessed(Request $request)
    {
        $request->setStatus(EnumStatus::PROCESSED);
        $this->entityManager->persist($request);
        $this->entityManager->flush();
    }

    public function persistCrawledUrl(Url $crawledUrl, array $newUrls, array $urlEmails)
    {
        $crawledUrl->setCrawledAt(new DateTime());
        $this->entityManager->persist($crawledUrl);

        foreach ($newUrls as $newUrl) {
            $this->createNewUrl($crawledUrl->getRequest(), $newUrl, $crawledUrl->getId());
        }
        foreach ($urlEmails as $urlEmail) {
            $email = new Email();
            $email->setEmail($urlEmail);
            $email->setUrl($crawledUrl);
            $this->entityManager->persist($email);
        }

        $this->entityManager->flush();
    }
}