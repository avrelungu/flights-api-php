<?php

namespace App\Http\Error\Exception;

interface ExtensibleExceptionInterface
{
    public function getExtensions(): array;
}