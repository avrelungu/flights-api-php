<?php

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Message\ServerRequestInterface;

interface ContentNegotiationInterface
{
    public function negotiate(ServerRequestInterface $request): ServerRequestInterface;
}