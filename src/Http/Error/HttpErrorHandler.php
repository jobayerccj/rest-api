<?php

declare(strict_types=1);

namespace App\Http\Error;

use App\Http\Error\Exception\ValidationException;
use App\Http\Middleware\ContentNegotiation\ContentType;
use Exception;
use Throwable;
use Slim\Handlers\ErrorHandler;
use Slim\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpMethodNotAllowedException;

class HttpErrorHandler extends ErrorHandler
{
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;
        
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getDescription();

            
            if ($exception instanceof HttpNotFoundException) {
                $problem = ProblemDetail::NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $problem = ProblemDetail::METHOD_NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $problem = ProblemDetail::UNAUTHORIZED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $problem = ProblemDetail::FORBIDDEN;
            } elseif ($exception instanceof HttpBadRequestException) {
                $problem = ProblemDetail::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $problem = ProblemDetail::NOT_IMPLEMENTED;
            } elseif ($exception instanceof ValidationException) {
                $problem = ProblemDetail::UNPROCESSABLE_ENTITY;
            }
        }


        dd($exception);
        if (
            !($exception instanceof HttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }
        dd($problem);
        $error = [
            'type' => $problem->type(),
            'title' => $exception->getTitle(),
            'detail' => $description,
            'instance' => $this->request->getUri()->getPath(),
        ];

        if ($exception instanceof ValidationException) {
            $error += $exception->getExtensions();
        }
        
        $payload = json_encode($error);
        $response = $this->responseFactory->createResponse($statusCode);
        $response = $response->withHeader('Content-Type', ContentType::JSON_PROBLEM->value);
        $response->getBody()->write($payload);
        return $response;
    }
}

