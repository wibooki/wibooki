<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Itinerary;
use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Demo controller.
 *
 * @Route("demo")
 */
class DemoController extends Controller
{
    /**
     * Lists all content entities.
     *
     * @Route("/mail/payment/admin/{id}", name="demo_mail_payment_admin")
     * @Method("GET")
     */
    public function mailPaymentAdminAction(Payment $payment)
    {
        return $this->render('mail/admin-itinerary.html.twig', array(
            'user' => $payment->getUser(),
            'itinerary' => $payment->getItinerary(),
            'payment' => $payment,
        ));
    }

    /**
     * Lists all content entities.
     *
     * @Route("/mail/payment/buy/{id}", name="demo_mail_payment_buy")
     * @Method("GET")
     */
    public function mailPaymenBuyAction(Payment $payment)
    {
        return $this->render('mail/buy-itinerary.html.twig', array(
            'user' => $payment->getUser(),
            'itinerary' => $payment->getItinerary(),
            'payment' => $payment,
        ));
    }
}
