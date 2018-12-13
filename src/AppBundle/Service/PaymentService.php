<?php
namespace AppBundle\Service;

use AppBundle\Entity\Itinerary;
use AppBundle\Entity\Payment;
use AppBundle\Entity\User;
use AppBundle\Repository\PaymentRepository;
use Symfony\Component\Translation\TranslatorInterface;

class PaymentService
{
    /** @var PaymentRepository */
    private $repository;
    
    /** @var SendMail */
    private $sendMailService;

    /** @var TranslatorInterface */
    private $translator;

    function __construct(
        $repository,
        $sendMailService,
        $translator
    ) {
        $this->repository = $repository;
        $this->sendMailService = $sendMailService;
        $this->translator = $translator;
    }

    /**
     * @param $storage
     * @param Itinerary $itinerary
     * @param User $user
     *
     * @return Payment
     */
    public function registerPayment($storage, Itinerary $itinerary, User $user)
    {
        /** @var Payment $payment */
        $payment = $storage->create();

        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount($itinerary->getPrice()); // 1.23 EUR
        $payment->setDescription($this->translator->trans('payment.description', [], 'buy'));
        $payment->setClientId($user->getId());
        $payment->setClientEmail($user->getEmail());
        $payment->setUser($user);
        $payment->setItinerary($itinerary);

        $storage->update($payment);

        return $payment;
    }

    /**
     * @param Payment $payment
     *
     * @return Payment
     */
    public function completePayment(Payment $payment)
    {
        $payment->setStatus(true);
        $this->repository->save($payment);

        return $payment;
    }

    /**
     * @param Itinerary $itinerary
     * @param User $user
     *
     * @return void
     */
    private function sendAdminItineraryEmail(Itinerary $itinerary, User $user, Payment $payment)
    {
        $this->sendMailService->send(
            'wibooki@gmail.com',
            $this->translator->trans(
                'mail.admin.title',
                ['%number%' => $itinerary->getId()],
                'itinerary'
            ),
            'admin-itinerary',
            [
                'user' => $user,
                'itinerary' => $itinerary,
                'payment' => $payment,
                'detailed' => false,
            ],
            'noreply@bookea.com'
        );
    }

    /**
     * @param Itinerary $itinerary
     * @param User $user
     *
     * @return void
     */
    private function sendConfirmItineraryEmail(Itinerary $itinerary, User $user, Payment $payment)
    {
        $this->sendMailService->send(
            $itinerary->getUser()->getEmail(),
            $this->translator->trans(
                'mail.admin.title',
                ['%number%' => $itinerary->getId()],
                'itinerary'
            ),
            'buy-itinerary',
            [
                'user' => $user,
                'itinerary' => $itinerary,
                'payment' => $payment,
                'detailed' => true,
            ],
            'noreply@wibooki.com'
        );
    }
}