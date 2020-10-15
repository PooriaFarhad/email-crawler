<?php

namespace App\CrawlerService;


use App\Lib\UrlHelper;
use Symfony\Component\DomCrawler\Crawler;

class DomCrawlerLookup extends AbstractWebLookup implements WebLookupInterface
{
    public function urlLookup(string $domain, string $content): array
    {
        $crawler = new Crawler($content);
        $items = $crawler->filterXPath('//a/@href');
        $urls = [];

        foreach ($items as $item) {
            if (strpos($item->nodeValue, '@')) {
                continue;
            }
            if (strpos($item->nodeValue, ';')) {
                continue;
            }
            $urlComponents = parse_url($item->nodeValue);
            if (!$urlComponents) {
                continue;
            }
            if (isset($urlComponents['host'])) {
                $host = UrlHelper::removeHostPrefix($urlComponents['host']);
                if ($host != $domain) {
                    continue;
                }
            }
            $urls[] = UrlHelper::getPathAndQuery($urlComponents);
        }

        return $urls;
    }
}