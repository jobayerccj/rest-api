<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

enum ContentType: string
{
    case JSON = 'application/json';
    case XML = 'application/xml';
    case HTML = 'text/html';
    case TEXT = 'text/plain';

    public function format(): string
    {
        return match ($this) {
            self::JSON => 'json',
            self::XML => 'xml',
            self::HTML => 'html',
            self::TEXT => 'txt',
        };
    }
}