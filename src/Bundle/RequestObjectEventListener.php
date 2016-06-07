<?php

namespace Fesor\RequestObject\Bundle;

use Fesor\RequestObject\RequestBinder;
use Fesor\RequestObject\RequestObjectBinder;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class RequestObjectEventListener
{
    private $requestBinder;

    /**
     * RequestObjectEventListener constructor.
     * @param RequestObjectBinder $requestBinder
     */
    public function __construct(RequestObjectBinder $requestBinder)
    {
        $this->requestBinder = $requestBinder;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        $errorResponse = $this->requestBinder->bind($request, $controller);

        if (null === $errorResponse) {
            return;
        }

        $event->setController(function () use ($errorResponse) {
            return $errorResponse;
        });
    }
}
