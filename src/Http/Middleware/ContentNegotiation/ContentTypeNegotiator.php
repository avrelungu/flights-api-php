<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Message\ServerRequestInterface;

class ContentTypeNegotiator implements ContentNegotiationInterface
{
    public function negotiate(ServerRequestInterface $request): ServerRequestInterface
    {
        $accept = $request->getHeaderLine('accept');
        
        $requestedFormats = explode(',', $accept);

        foreach($requestedFormats as $requestedFormat) {
            if ($format = ContentType::tryFrom($requestedFormat)) {
                break;
            }
        }

        $contentType = ($format ?? ContentType::JSON);

        $request = $request->withAttribute('Content-Type', $contentType);

        return $request;
    }
}