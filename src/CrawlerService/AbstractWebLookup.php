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
}