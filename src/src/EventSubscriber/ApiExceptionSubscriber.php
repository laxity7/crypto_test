<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private string $kernelEnv;

    public function __construct(string $kernelEnv)
    {
        $this->kernelEnv = $kernelEnv;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 100],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        $isApiRequest = str_starts_with($request->getPathInfo(), '/api/') ||
            $request->headers->get('Accept') === 'application/json' ||
            $request->headers->get('Content-Type') === 'application/json';

        if (!$isApiRequest) {
            return;
        }

        $exception = $event->getThrowable();

        $errorMessage = 'An internal server error occurred.';
        if ($this->kernelEnv === 'dev') {
            $errorMessage = $exception->getMessage();
        } elseif ($exception instanceof HttpExceptionInterface) {
            $errorMessage = $exception->getMessage();
        }

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        $data = [
            'error' => $errorMessage,
            'code'  => $statusCode,
        ];

        if ($this->kernelEnv === 'dev') {
            $data['trace'] = $exception->getTraceAsString();
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
        }

        $response = new JsonResponse($data, $statusCode);
        $response->headers->set('Content-Type', 'application/json');

        $event->setResponse($response);
        $event->stopPropagation();
    }
}
