<?php

namespace App\CrawlerService;

abstract class AbstractWebLookup
{
    public function fetchContent(string $url): ?string
    {
        return file_get_contents($url);
    }

    public function emailLookup(string $content): array
    {
        if (!preg_match_all('/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/', $content, $matches)) {
            return [];
        }

        return array_unique(reset($matches));
    }
}