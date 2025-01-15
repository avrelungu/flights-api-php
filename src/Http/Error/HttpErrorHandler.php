<?php

declare(strict_types=1);

namespace App\Http\Error;

use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Exception;
use Throwable;

class HttpErrorHandler extends ErrorHandler
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    
    protected function respond(): ResponseInterface
    {
        dd($this->exception->getMessage());
        $exception = $this->exception;
        $statusCode = 500;
        $problem = self::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';
        $title = '500 Internal Server Error';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();
            $title = $exception->getTitle();

            if ($exception instanceof HttpNotFoundException) {
                $problem = ErrorType::NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $problem = ErrorType::METHOD_NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $problem = ErrorType::UNAUTHORIZED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $problem = ErrorType::UNAUTHORIZED;
            } elseif ($exception instanceof HttpBadRequestException) {
                $problem = ErrorType::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $problem = ErrorType::NOT_IMPLEMENTED;
            }
        }

        if (
            !($exception instanceof HttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        $error = [
            'type' => $problem->type(),
            'title' => $title,
            'detail' => $description,
            'instance' => $this->request->getUri()->getPath(),
            # extensions (examples) - Use custom exceptions for these and array merge the extensions
            'errors' => [
                [
                    "detail" => "must be a positive integer",
                    "pointer" => "#/age"
                ],
                [
                    "detail" => "must be a positive integer",
                    "pointer" => "#/age"
                ],
            ]
        ];
        
        $payload = json_encode($error, JSON_PRETTY_PRINT);
        
        $response = $this->responseFactory->createResponse($statusCode);        
        $response->getBody()->write($payload);
        $response->withHeader('Content-Type', 'application/problem+json');
        
        return $response;
    }
} 