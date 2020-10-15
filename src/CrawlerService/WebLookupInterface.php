<?php

namespace App\CrawlerService;


interface WebLookupInterface
{
    public function fetchContent(string $url): string;

    public function emailLookup(string $content): array;

    public function urlLookup(string $domain, string $content): array;
}