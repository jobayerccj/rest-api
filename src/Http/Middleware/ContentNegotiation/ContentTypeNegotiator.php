<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Message\ServerRequestInterface;

class ContentTypeNegotiator implements ContentNegotiation
{
    public function negotiate(ServerRequestInterface $request): ServerRequestInterface
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        $requestedFormats = explode(',', $acceptHeader);

        foreach ($requestedFormats as $requestedFormat) {
            if ($format = ContentType::tryFrom(trim($requestedFormat))) {
                break;
            }
        }

        $contentType = ($format ?? ContentType::JSON);
        return $request->withAttribute('content-type', $contentType);
    }
}