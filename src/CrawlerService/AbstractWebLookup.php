<?php

namespace App\CrawlerService;

abstract class AbstractWebLookup
{
    public function fetchContent(string $url): string
    {
        $headers = get_headers($url, 1);
        $contentType = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
        if (strpos($contentType, 'text/html') === false) {
            return '';
        }

        return @file_get_contents($url);
    }

    public function emailLookup(string $content): array
    {
        if (!preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/', $content, $matches)) {
            return [];
        }

        return array_unique(reset($matches));
    }
}