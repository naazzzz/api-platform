<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BaseEntity;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['doUpdateDateTime', EventPriorities::PRE_WRITE],
        ];    }

    public function doUpdateDateTime(ViewEvent $event):void
    {
        $entity=$event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$entity instanceof BaseEntity || Request::METHOD_POST == $method) {
            return;
        }
        $entity->setDateUpdate(new \DateTime());
    }
}