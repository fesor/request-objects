<?php

namespace Fesor\RequestObject\Bundle;

use Fesor\RequestObject\ErrorResponseProvider;
use Fesor\RequestObject\InvalidRequestPayloadException;
use Fesor\RequestObject\Request;
use Fesor\RequestObject\RequestBinder;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestObjectEventListener
{
    private $requestBinder;

    /**
     * RequestObjectEventListener constructor.
     * @param RequestBinder $requestBinder
     */
    public function __construct(RequestBinder $requestBinder)
    {
        $this->requestBinder = $requestBinder;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();
        $controllerReflection = new \ReflectionClass($controller[0]);
        $actionReflection = $controllerReflection->getMethod($controller[1]);
        $arguments = $actionReflection->getParameters();

        $requestObjectArgument = $this->findSubtypeArgument($arguments, Request::class);
        if (null === $requestObjectArgument) {
            return;
        }
        $errorListArgument = $this->findSubtypeArgument($arguments, ConstraintViolationListInterface::class);
        $className = $requestObjectArgument->getClass()->getName();

        if (!$errorListArgument) {
            $bindedRequest = $this->requestBinder->bindOrFail($className, $request);
        } else {
            $bindedRequest = $this->requestBinder->bind($className, $request);
        }

        $request->attributes->set($requestObjectArgument->getName(), $bindedRequest->getRequestObject());
        if ($errorListArgument) {
            $request->attributes->set($errorListArgument->getName(), $bindedRequest->getErrors());
        }
    }
    
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof InvalidRequestPayloadException) {
            return;
        }
        
        $requestObject = $exception->getRequestObject();
        if (!$requestObject instanceof ErrorResponseProvider) {
            return;
        }

        $event->setResponse(
            $requestObject->getErrorResponse(
                $exception->getErrors()
            )
        );
    }

    /**
     * @param \ReflectionParameter[] $arguments
     * @param string $subtype
     * @return \ReflectionParameter
     */
    private function findSubtypeArgument(array $arguments, $subtype)
    {
        foreach ($arguments as $argument)
        {
            if (!($className = $argument->getClass())) {
                continue;
            }
            $className = $className->getName();
            if (is_a($className, $subtype, true)) {
                return $argument;
            }
        }

        return null;
    }
}
