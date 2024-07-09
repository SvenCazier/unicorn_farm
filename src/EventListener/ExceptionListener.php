<?php

// src/EventListener/ExceptionListener.php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($_ENV["DISABLE_EXCEPTION_LISTENER"] ?? false) {
            return;
        }

        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $response = new JsonResponse([
            '@id' => '/api/errors',
            '@type' => 'hydra:Error',
            "title" => "An error occurred",
            "detail" => $exception->getMessage(),
            'status' => $statusCode,
            'type' => '/errors/' . $statusCode,
            'hydra:title' => 'An error occurred',
            'hydra:description' => $exception->getMessage(),
        ]);

        $response->setStatusCode($statusCode);

        $event->setResponse($response);
    }
}
