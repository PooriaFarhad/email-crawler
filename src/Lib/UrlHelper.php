<?php

namespace App\Lib;

class UrlHelper
{
    public static function getPathAndQuery(array $urlComponents): string
    {
        $result = '';
        if (isset($urlComponents['path'])) {
            $result .= $urlComponents['path'];
        }
        if (isset($urlComponents['query'])) {
            $result .= '?' . $urlComponents['query'];
        }
        if(empty($result)) {
            $result .= '/';
        }

        return $result;
    }

    public static function getCompleteUrl(string $host, string $pathQuery): string
    {
        return 'http://' . $host . '/' . $pathQuery;
    }

    public static function removeHostPrefix(string $domain)
    {
        return preg_replace('/^www\./', '', $domain);
    }
}