<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Content;
use AppBundle\Entity\Itinerary;
use AppBundle\Entity\Payment;

use AppBundle\Repository\ContentRepository;
use AppBundle\Repository\ItineraryRepository;
use AppBundle\Service\ItineraryService;
use AppBundle\Service\PaymentService;
use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Booking controller.
 *
 * @Route("booking")
 */
class BookingController extends Controller
{
    /**
     * Finds and displays a booking entity.
     *
     * @param Request $request
     *
     * @Route("/get/{slug}/", name="booking_clone")
     *
     * @return Response
     */
    public function copyAction(Request $request, Itinerary $itinerary)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var ItineraryService $itineraryService */
        $itineraryService = $this->get('app.itinerary.service');
        $newItinerary = $itineraryService->copy($itinerary,$user);

        $storage = $this->get('payum')->getStorage('AppBundle\Entity\Payment');
        /** @var PaymentService $paymentService */
        $paymentService = $this->get('app.payment.service');
        $payment = $paymentService->registerPayment($storage,$itinerary,$user);

        return $this->redirectToRoute('itinerary_show', array('slug' => $newItinerary->getSlug()));
    }

    /**
     * Finds and displays a booking entity.
     *
     * @param Request $request
     *
     * @Route("/buy/{slug}/prepare", name="booking_buy")
     *
     * @return Response
     */
    public function buyPrepareAction(Request $request, Itinerary $itinerary)
    {
        if (! $itinerary->getPaid()) {
            return $this->redirectToRoute('booking_clone', array('slug' => $itinerary->getSlug()));
        }
        if (! $itinerary->getPrice() > 0) {
            return $this->redirectToRoute('itinerary_show', array('slug' => $itinerary->getSlug()));
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $gatewayName = 'paypal_express_checkout';

        $storage = $this->get('payum')->getStorage('AppBundle\Entity\Payment');
        /** @var PaymentService $paymentService */
        $paymentService = $this->get('app.payment.service');
        $payment = $paymentService->registerPayment($storage,$itinerary,$user);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'booking_buy_done'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * Finds and displays a booking entity.
     *
     * @param Request $request
     *
     * @Route("/buy/done", name="booking_buy_done")
     *
     * @return Response
     */
    public function buyDoneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly.
        //$identity = $token->getDetails();
        //$payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));

        /** @var Payment $payment */
        $payment = $status->getFirstModel();

        $itinerary = $payment->getItinerary();

        if ($status->isCaptured()) {

            $user = $this->get('security.token_storage')->getToken()->getUser();

            /** @var PaymentService $paymentService */
            $paymentService = $this->get('app.payment.service');
            $paymentService->completePayment($payment);

            /** @var ItineraryService $itineraryService */
            $itineraryService = $this->get('app.itinerary.service');
            $newItinerary = $itineraryService->copy($itinerary,$user);

            return $this->redirectToRoute('itinerary_show', array('slug' => $newItinerary->getSlug()));
        } else if ($status->isPending()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('payment.error.pending', [], 'buy')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('payment.error.pending', [], 'buy')
            );
        }

        return $this->redirectToRoute('itinerary_show', array('slug' => $itinerary->getSlug()));

    }

    /**
     * Finds and displays a booking entity.
     *
     * @param Request $request
     * @param string $reference
     *
     * @Route("/view/email/{reference}", name="booking_email_view")
     *
     * @return Response
     */
    public function emailViewAction(Request $request, $reference)
    {
        $bookingRepository = $this->getDoctrine()->getRepository('AppBundle:Booking');
        $booking = $bookingRepository->findOneBy(['reference'=>$reference]);

        return $this->render('mail/booking-details.html.twig', [
            'booking' => $booking,
            'detailed' => true
        ]);
    }

//    /**
//     * Finds and displays a booking entity.
//     *
//     * @param Request $request
//     * @param string $slug
//     * @param string $reference
//     *
//     * @Route("/{slug}/booking-book/{reference}", name="booking-book")
//     * @Method("POST")
//     *
//     * @return Response
//     */
//    public function bookAction(Request $request, $slug, $reference)
//    {
//        /** @var BookingService $bookingService */
//        $bookingService = $this->get('app.booking_service');
//        $booking = $bookingService->getBookingByReference($reference);
//        $userBooking = $bookingService->getUserBookingService();
//
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        /** @var UserHandler $userHandle */
//        $userHandle = $this->get('app.user.handler');
//
//        if (!$user instanceof User) {
//            /** @var UserQueryService $userQuery */
//            $userQuery = $this->get('app.user_query_service');
//            $email = !empty($request->request->all()['xsf_user']['email'])
//                ? $request->request->all()['xsf_user']['email']
//                : null;
//
//            $user = $userQuery->findOneBy(['email' => $email]);
//        }
//
//        try {
//            $session = new Session();
//            $session->clear();
//
//            if ($user instanceof User) {
//                $user = $userHandle->put($user, $request->request->all()['xsf_user']);
//            } else {
//                $user = $userHandle->post($request->request->all()['xsf_user']);
//                $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
//                $this->get('security.token_storage')->setToken($token);
//            }
//
//            echo "1";
//            $bookingService->addUser($booking, $user);
//
//            echo "2";
//            /** @var AvailabilityQueryService $availabilityQuery */
//            $availabilityQuery = $this->get('app.availability_query_service');
//            $availabilityQuery->discountAvailabilityRooms($booking, $bookingService->getUserBookingService());
//            echo "3";
//            $bookingService->completeBooking($booking);
//            echo "4";
//
//            $this->sendConfirmBookingEmail($booking);
//            echo "5";
//
//        } catch (InvalidFormException $e) {
//            $validator = $this->get('validator');
//            /** @var ConstraintViolationList $errors */
//            $errors = $validator->validate($e->getForm());
//            $errorsTextArray = $this->getErrorsAsArray($errors);
//
//            /** @var User $user */
//            $user = $e->getForm()->getData();
//            $session = new Session();
//            $session->set('userForm', $user->_toArray());
//            echo $user->_toArray();
//            die();
//
//            $defaultMessage = $this->get('translator')->trans('confirm.error.booking-general', [], 'reservation') . ' ' . $e->getMessage();
//            $errorsTextArray = !empty($errorsTextArray) ? $errorsTextArray : [$defaultMessage];
//            $session->set('formErrors', json_encode($errorsTextArray));
//            return $this->redirectToRoute('booking-confirm',
//                [
//                    'slug' => $booking->getHotel()->getSlug(),
//                    'reference' => $booking->getReference(),
//                    'xsf_search' => $userBooking,
//                ]
//            );
//        } catch (\Exception $e) {
//            $session = new Session();
//            $session->set('formErrors', json_encode([$e->getMessage()]));
//            echo json_encode([$e->getMessage()]);
//            die();
//            return $this->redirectToRoute('booking-confirm',
//                [
//                    'slug' => $booking->getHotel()->getSlug(),
//                    'reference' => $booking->getReference(),
//                    'xsf_search' => $userBooking,
//                ]
//            );
//        }
//
//        return $this->redirectToRoute('booking_show', [
//            'reference' => $booking->getReference(),
//        ]);
//    }

    /**
     * @param ConstraintViolationList $errorsList
     *
     * @return array
     */
    private function getErrorsAsArray(ConstraintViolationList $errorsList)
    {
        $errorsStringArray = [];
        /** @var ConstraintViolation $error */
        foreach ($errorsList as $error) {

            $errorsStringArray[] = $error->getMessage();
        }

        return $errorsStringArray;
    }
}
