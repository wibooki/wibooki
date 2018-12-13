<?php
namespace AppBundle\Listener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class ChangePasswordListener implements EventSubscriberInterface {
    private $router;

    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordCompleted',
        ];
    }

    public function onChangePasswordCompleted(FormEvent $event) {
        $url = $this->router->generate('change_password_completed');
        $event->setResponse(new RedirectResponse($url));
    }
}