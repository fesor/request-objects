<?php

namespace Fesor\RequestObject\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RequestObjectEventListener
{
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
