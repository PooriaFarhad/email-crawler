<?php

namespace App\Lib;

class Pager
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 20;

    private $page;
    private $limit;

    public function __construct(?int $page = self::DEFAULT_PAGE, ?int $limit = self::DEFAULT_LIMIT)
    {
        $this->page = $page ?: self::DEFAULT_PAGE;
        $this->limit = $limit ?: self::DEFAULT_LIMIT;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}