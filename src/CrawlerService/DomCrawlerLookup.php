<?php

namespace App\CrawlerService;


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
            if (isset($urlComponents['host'])) {
                $host = preg_replace('/^www\./', '', $urlComponents['host']);
                if ($host != $domain) {
                    continue;
                }
            }
            $urls[] = $urlComponents['path'];
        }

        return $urls;
    }
}