<?php

namespace App\CrawlerService;

use App\Lib\UrlHelper;
use DOMDocument;

class WebLookup extends AbstractWebLookup implements WebLookupInterface
{
    public function urlLookup(string $domain, string $content): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $items = $dom->getElementsByTagName('a');
        $urls = [];
        foreach ($items as $item) {
            $href = trim($item->getAttribute('href'));
            if (strpos($href, '@')) {
                continue;
            }
            if (strpos($href, ';')) {
                continue;
            }
            $urlComponents = parse_url($href);
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

        return array_unique($urls);
    }

    public function emailLookup(string $content): array
    {
        if (!preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/', $content, $matches)) {
            return [];
        }

        return array_unique(reset($matches));
    }
}