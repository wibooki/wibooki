<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Itinerary;
use AppBundle\Entity\User;
use AppBundle\Repository\ItineraryRepository;
use AppBundle\Service\ItineraryService;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Itinerary controller.
 *
 * @Route("itinerary")
 */
class ItineraryController extends Controller
{
    /**
     * Lists all itinerary entities.
     *
     * @Route("/", name="itinerary_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $itineraries = $em->getRepository('AppBundle:Itinerary')->findAllPublic($user);

        return $this->render('itinerary/index.html.twig', array(
            'itineraries' => $itineraries,
        ));
    }

    /**
     * Creates a new itinerary entity.
     *
     * @Route("/new", name="itinerary_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $itinerary = new Itinerary();
        $itinerary->setUser($user);
        $form = $this->createForm('AppBundle\Form\ItineraryType', $itinerary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($itinerary);
            $em->flush($itinerary);

            $this->get('session')->getFlashBag()->add('success', 'El itinerario ha sido creado.');
        }

        return $this->redirectToRoute('fos_user_profile_show');
    }

    /**
     * Finds and displays a itinerary entity.
     *
     * @Route("/{slug}", name="itinerary_show")
     * @Method("GET")
     */
    public function showAction(Itinerary $itinerary)
    {
//        $user = $this->getUser();
//        if ($itinerary->isPrivate() && (!is_object($user) || !$user instanceof UserInterface)) {
//            throw new AccessDeniedException('This user does not have access to this section.');
//        }

        $deleteForm = $this->createDeleteForm($itinerary);
        $editForm = $this->createForm('AppBundle\Form\ItineraryType', $itinerary, [
            'action' => $this->generateUrl('itinerary_edit',['id' => $itinerary->getId()]),
        ]);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var ItineraryService $itineraryService */
        $itineraryService = $this->get('app.itinerary.service');
        $isCopied = $itineraryService->isCopied($itinerary,$user);

        return $this->render('itinerary/show.html.twig', array(
            'isCopied' => $isCopied,
            'itinerary' => $itinerary,
            'itinerary_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing itinerary entity.
     *
     * @Route("/{id}/edit", name="itinerary_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Itinerary $itinerary)
    {
        $user = $this->getUser();
        $itineraryUser = $itinerary->getUser();

        if (!is_object($user)
            || !$user instanceof UserInterface
            || !is_object($itineraryUser)
            || !$itineraryUser instanceof UserInterface
            || $user !== $itineraryUser
        ) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $editForm = $this->createForm('AppBundle\Form\ItineraryType', $itinerary);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('itinerary_show', array('slug' => $itinerary->getSlug()));
        }

        return $this->redirectToRoute('fos_user_profile_show');
    }

    /**
     * Deletes a itinerary entity.
     *
     * @Route("/{id}", name="itinerary_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Itinerary $itinerary)
    {
        $user = $this->getUser();
        $itineraryUser = $itinerary->getUser();

        if (!is_object($user)
            || !$user instanceof UserInterface
            || !is_object($itineraryUser)
            || !$itineraryUser instanceof UserInterface
            || $user !== $itineraryUser
        ) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createDeleteForm($itinerary);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($itinerary);
            $em->flush($itinerary);
        }

        return $this->redirectToRoute('fos_user_profile_show');
    }

    /**
     * Creates a form to delete a itinerary entity.
     *
     * @param Itinerary $itinerary The itinerary entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Itinerary $itinerary)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('itinerary_delete', array('id' => $itinerary->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
