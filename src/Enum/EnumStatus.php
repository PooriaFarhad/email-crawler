<?php

namespace App\Enum;

class EnumStatus extends EnumType
{
    protected $name = self::class;

    const NEW = 'new';
    const PROCESSING = 'processing';
    const PROCESSED = 'processed';
    protected $values = [
        self::NEW,
        self::PROCESSING,
        self::PROCESSED,
    ];
}