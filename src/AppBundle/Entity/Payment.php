<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table(name="payum__payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 */
class Payment extends BasePayment
{
    const DATE_EXPIRATION = 'PT15M';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Itinerary")
     * @ORM\JoinColumn(name="itinerary_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $itinerary;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="reservation_date", type="string", length=255)
     */
    private $reservationDate;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="expiration_date", type="string", length=255)
     */
    private $expirationDate;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="canceled_date", type="string", length=255, nullable=true)
     */
    private $canceledDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $time = new \DateTime();
        $this->reservationDate = $time->format('Y-m-d H:i');

        $time->add(new \DateInterval(self::DATE_EXPIRATION));
        $this->expirationDate = $time->format('Y-m-d H:i');

        $this->status = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Payment
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set itinerary
     *
     * @param \AppBundle\Entity\Itinerary $itinerary
     *
     * @return Payment
     */
    public function setItinerary(\AppBundle\Entity\Itinerary $itinerary = null)
    {
        $this->itinerary = $itinerary;

        return $this;
    }

    /**
     * Get itinerary
     *
     * @return \AppBundle\Entity\Itinerary
     */
    public function getItinerary()
    {
        return $this->itinerary;
    }

    /**
     * Set reservationDate
     *
     * @param string $reservationDate
     *
     * @return Payment
     */
    public function setReservationDate($reservationDate)
    {
        $this->reservationDate = $reservationDate;

        return $this;
    }

    /**
     * Get reservationDate
     *
     * @return string
     */
    public function getReservationDate()
    {
        return $this->reservationDate;
    }

    /**
     * Set expirationDate
     *
     * @param string $expirationDate
     *
     * @return Payment
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set canceledDate
     *
     * @param string $canceledDate
     *
     * @return Payment
     */
    public function setCanceledDate($canceledDate)
    {
        $this->canceledDate = $canceledDate;

        return $this;
    }

    /**
     * Get canceledDate
     *
     * @return string
     */
    public function getCanceledDate()
    {
        return $this->canceledDate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
}
