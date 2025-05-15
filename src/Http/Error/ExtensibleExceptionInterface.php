<?php

declare(strict_types=1);

namespace App\Http\Error;

interface ExtensibleExceptionInterface
{
    public function getExtensions(): array;
}