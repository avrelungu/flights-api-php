<?php

declare(strict_types=1);

namespace App\Http\Error;

enum ErrorType: int
{
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case UNPROCESSABLE_CONTENT = 422;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;

    public function type()
    {
        return match($this) {
            self::NOT_FOUND => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.4',
            self::METHOD_NOT_ALLOWED => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.5',
            self::UNAUTHORIZED => 'https://datatracker.ietf.org/doc/html/rfc7235#section-3.1',
            self::BAD_REQUEST => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.1',
            self::NOT_IMPLEMENTED => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.2',
            self::UNPROCESSABLE_CONTENT => 'https://datatracker.ietf.org/doc/html/rfc9110#name-422-unprocessable-content',
            self::INTERNAL_SERVER_ERROR => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.1'
        };
    }
}