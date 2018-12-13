<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use AppBundle\Entity\Itinerary;
use AppBundle\Form\ContentType;
use AppBundle\Repository\ContentRepository;
use AppBundle\Repository\ItineraryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Content controller.
 *
 * @Route("content")
 */
class ContentController extends Controller
{
    /**
     * Lists all content entities.
     *
     * @Route("/", name="content_index")
     * @Method("GET")
     */
    /*public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contents = $em->getRepository('AppBundle:Content')->findAll();

        return $this->render('content/index.html.twig', array(
            'contents' => $contents,
        ));
    }*/

    /**
     * Creates a new content entity.
     *
     * @Route("/new", name="content_new", defaults={"iId" = 0})
     * @Route("/new/{iId}", name="content_new_itinerary")
     *
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request,$iId)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $id = $user->getId();
        $itinerary = false;
        $folderHome = hash_hmac('sha256', $id, 'K7qq4Ork0R602zb9QLwfq4z1476P9Y54');

        $content = new Content();
        $content->setUser($user);
        if ($iId != 0) {
            /** @var ItineraryRepository $itineraryRepository */
            $itineraryRepository = $this->get('app.itinerary.repository');
            $itinerary = $itineraryRepository->find($iId);
            if (! $itinerary) {
                throw $this->createNotFoundException('No itinerary found ');
            }
            $content->addItinerary($itinerary);
            $action = $this->generateUrl('content_new_itinerary',['iId'=>$itinerary->getId()]);
        }
        else {
            $action = $this->generateUrl('content_new');
        }

		$form = $this->createForm(
            ContentType::class,
            $content,
            [
                'home_folder' => $folderHome,
                'action' => $action,
                'user' => $user
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($content->getItineraries() as &$itinerary) {
                $itinerary->setUser($user);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            if ($itinerary) {
                return $this->redirectToRoute('itinerary_show',['slug'=>$itinerary->getSlug()]);
            }
            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render('content/new.html.twig', array(
            'content' => $content,
            'form' => $form->createView(),
			'user' => $user
        ));
    }

    /**
     * Finds and displays a content entity.
     *
     * @Route("/{slug}", name="content_show")
     * @Method("GET")
     */
    public function showAction(Content $content)
    {
        $user = $this->getUser();
        if (! $content->isDemo() && (!is_object($user) || !$user instanceof UserInterface)) {
            throw new AccessDeniedException('- This user does not have access to this section.');
        }

        return $this->render('content/show.html.twig', array(
            'content' => $content
        ));
    }

    /**
     * Displays a form to edit an existing content entity.
     *
     * @Route("/{id}/edit", name="content_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Content $content)
    {
        $user = $this->getUser();
        $contentUser = $content->getUser();

        if (!is_object($user)
            || !$user instanceof UserInterface
            || !is_object($contentUser)
            || !$contentUser instanceof UserInterface
            || $user !== $contentUser
        ) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        foreach ($content->getItineraries() as &$itinerary) {
            $itinerary->setUser($user);
        }

        $id = $user->getId();
        $folderHome = hash_hmac('sha256', $id, 'K7qq4Ork0R602zb9QLwfq4z1476P9Y54');

        $content->setLastModificationDate();

        $editForm = $this->createForm(
            ContentType::class,
            $content,
            [
                'home_folder' => $folderHome,
                'user' => $user
            ]
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('content_edit', array('id' => $content->getId()));
        }

        $deleteForm = $this->createDeleteForm($content);

        return $this->render('content/edit.html.twig', array(
            'content' => $content,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Deletes a content entity.
     *
     * @Route("/{id}/edit", name="content_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Content $content)
    {
        $user = $this->getUser();
        $contentUser = $content->getUser();

        if (!is_object($user)
            || !$user instanceof UserInterface
            || !is_object($contentUser)
            || !$contentUser instanceof UserInterface
            || $user !== $contentUser
        ) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createDeleteForm($content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($content);
            $em->flush();
        }

        return $this->redirectToRoute('fos_user_profile_show');
    }

    /**
     * Creates a form to delete a content entity.
     *
     * @param Content $content The content entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Content $content)
    {
        $user = $this->getUser();
        $contentUser = $content->getUser();

        if (!is_object($user)
            || !$user instanceof UserInterface
            || !is_object($contentUser)
            || !$contentUser instanceof UserInterface
            || $user !== $contentUser
        ) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('content_delete', array('id' => $content->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
