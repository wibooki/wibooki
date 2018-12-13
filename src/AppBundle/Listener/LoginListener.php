<?php
namespace AppBundle\Listener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LoginListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
        );
    }

    public function onLogin($event)
    {
        $user = null;

        if ($event instanceof UserEvent) {
            $user = $event->getUser();
        }
        if ($event instanceof InteractiveLoginEvent) {
            $user = $event->getAuthenticationToken()->getUser();
        }

        if (!empty($user)) {
            $id = $user->getId();
            $folderHome = hash_hmac('sha256', $id, 'K7qq4Ork0R602zb9QLwfq4z1476P9Y54');

            $fs = new Filesystem();

            try {
                $exists = $fs->exists(__DIR__ . '/../../../web/uploads/' . $folderHome);
                if (!$exists) {
                    $fs->mkdir(__DIR__ . '/../../../web/uploads/' . $folderHome);
                }
            } catch (IOExceptionInterface $e) {
                //echo "An error occurred while creating your directory at ".$e->getPath();
            }
        }
    }
}
