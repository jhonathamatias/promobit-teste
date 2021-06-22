<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Repository\UsersRepository;
use App\Services\Auth;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(ControllerEvent $event)
    {
        $controller = $event->getController();

        $methodType = $event->getRequest()->getMethod();

        $headers = $event->getRequest()->headers;

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($this->shouldValidateToken($controller, $methodType)) {
            // ... validação JWT
            $this->validateToken((string)$headers->get('Authorization'));
        }

        $event->getRequest()->attributes->set('token', (string)$headers->get('Authorization'));
    }

    protected function validateToken(string $token)
    {
        try {
            
            Auth::verifyToken($token);

        } catch(\Exception $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }

    protected function shouldValidateToken($controller, string $method)
    {
        return $controller instanceof TokenAuthenticatedController;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelRequest',
        ];
    }
}
