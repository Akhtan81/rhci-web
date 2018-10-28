<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -1024],
            KernelEvents::RESPONSE => ['onBadResponse', -1024],
        ];
    }

    private function isProd()
    {
        $env = $this->container->getParameter('kernel.environment');
        return $env === 'prod';
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $traceLine = '';

        $status = intval($exception->getCode());

        if ($exception instanceof AccessDeniedHttpException) {
            $status = 403;
        }

        if ($exception instanceof NotFoundHttpException) {
            $status = 404;
        }

        $content = $exception->getMessage();
        $traces = $exception->getTrace();

        foreach ($traces as $trace) {
            if (isset($trace['file']) && isset($trace['line'])) {
                $traceLine .= "\n" . $trace['file'] . "::" . $trace['line'];
            }
        }

        $messageTemplates = [
            "*%s*\n*Path*: `%s`\n*Code:* `%s`\n*Content*: %s\n*File*: %s\n*Line*: %s\n*IP*: %s\n*Trace*: %s",
        ];

        $message = sprintf(
            $messageTemplates[mt_rand(0, count($messageTemplates) - 1)],
            get_class($exception),
            $event->getRequest()->getPathInfo(),
            $status,
            $content,
            $exception->getFile(),
            $exception->getLine(),
            $event->getRequest()->getClientIp(),
            $traceLine
        );

        $this->notify($message);
    }

    public function onBadResponse(FilterResponseEvent $event)
    {

        $response = $event->getResponse();
        $request = $event->getRequest();
        $status = intval($response->getStatusCode());

        if ($status < 300) return;
        if (!in_array($status, [400, 404, 500, 501], true)) return;

        $messageTemplates = [
            "`%s %s`\n*Query*: %s\n*Body*: %s\n*IP*: %s\n*Code:* `%s`\n*Response*: %s",
        ];

        $contentType = $response->headers->get('Content-Type');

        $content = $response->getContent();
        if ($contentType === 'text/html'
            || strpos($content, '<html') !== false) {
            $content = '<html>...</html>';
        }

        $message = sprintf(
            $messageTemplates[mt_rand(0, count($messageTemplates) - 1)],
            $request->getMethod(),
            $request->getPathInfo(),
            json_encode($request->query->all()),
            $request->getContent(),
            $request->getClientIp(),

            $status,
            $content
        );

        $this->notify($message);
    }

    private function notify($message)
    {
        if (!$this->isProd()) return null;

        $accessToken = $this->container->getParameter('slack_request_webhook');
        if (!$accessToken) return null;

        $data = json_encode([
            'text' => $message
        ]);

        $ch = curl_init('https://hooks.slack.com/services/' . $accessToken);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [
            'status' => $code,
            'content' => $content,
        ];
    }
}