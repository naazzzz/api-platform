<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterEventSubscriber implements EventSubscriberInterface
{


    public function __construct(
        public RateLimiterFactory $anonymousApiLimiter,
        private readonly Security           $security,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['checkLimit', EventPriorities::PRE_WRITE]
        ];
    }

    public function checkLimit(RequestEvent $event): void
    {
        $limiter = $this->anonymousApiLimiter->create($event->getRequest()->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

    }
}