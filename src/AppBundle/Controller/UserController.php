<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * @Route("/{username}", name="user_view")
     */
    public function showAction(User $user)
    {
        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            "user" => $user,
        ));
    }
}
