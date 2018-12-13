<?php

namespace AppBundle\Controller;

use AppBundle\Form\SearchType;
use AppBundle\Repository\ContentRepository;
use AppBundle\Repository\ItineraryRepository;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $response = $this->forward('FOSUserBundle:Registration:register');
        } else {
            $response = $this->forward('FOSUserBundle:Profile:show');
        }
        return $response;
    }

    /**
     * @Route("s/", name="search")
     */
    public function searchAction(Request $request)
    {
        $key = $request->query->get('q', null);
        if ($key == null) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(SearchType::class);

        /** @var ItineraryRepository $itineraryRepository */
        $itineraryRepository = $this->get('app.itinerary.repository');
        $itineraries = $itineraryRepository->findByKeyword($key);

        /** @var ContentRepository $contentRepository */
        $contentRepository = $this->get('app.content.repository');
        $contents = $contentRepository->findByKeyword($key);

        /** @var UserRepository $userRepository */
        $userRepository = $this->get('app.user.repository');
        $users = $userRepository->findByKeyword($key);

        return $this->render('default/search.html.twig', [
            'key' => $key,
            'form' => $form->createView(),
            'itineraries' => $itineraries,
            'contents' => $contents,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/politica-de-privacidad", name="politica-de-privacidad")
     */
    public function politicaDePrivacidadAction(Request $request)
    {
        $user = $this->getUser();
        return $this->render('sites/politica-de-privacidad.html.twig', [
            'user'  => $user
        ]);
    }

    /**
     * @Route("/condiciones-de-uso", name="condiciones-de-uso")
     */
    public function condicionesDeUsoAction(Request $request)
    {
        $user = $this->getUser();
        return $this->render('sites/condiciones-de-uso.html.twig', [
            'user'  => $user
        ]);
    }

    /**
     * @Route("/acerca-de", name="acerca-de")
     */
    public function acercaDeAction(Request $request)
    {
        $user = $this->getUser();
        return $this->render('sites/acerca-de.html.twig', [
            'user'  => $user
        ]);
    }


    /**
     * @Route("/password-completed", name="change_password_completed")
     */
    public function changePasswordCompletedAction(Request $request)
    {
        $response = $this->forward('FOSUserBundle:ChangePassword:changePassword');
        return $response;
    }


}
