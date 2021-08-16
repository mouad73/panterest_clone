<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $urlGenertor;
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenertor)
    {
        $this->urlGenertor = $urlGenertor;
        $this->flashBag = $flashBag;
    }
    public function onLogoutEvent(LogoutEvent $event)
    {
        $this->flashBag->add('success', 'Bye Bye '. $event->getToken()->getUser()->getFullName());
        $event->setResponse(new RedirectResponse($this->urlGenertor->generate('app_home')));
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
