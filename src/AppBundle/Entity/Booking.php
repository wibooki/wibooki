<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Booking
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookingRepository")
 */
class Booking
{
    const DATE_EXPIRATION = 'PT15M';

    /* Booking done but pending by somenthing */
    const STATUS_PENDING = 0;
    /* Booking complete (the user used the room) */
    const STATUS_COMPLETED = 1;
    /* Booking canceled (the user or admin cancel the booking) */
    const STATUS_CANCELED = 2;
    /* Booking (the user did not use the room) */
    const STATUS_ACCEPTED = 3;

    public static $bookingStatus = array(
        self::STATUS_PENDING => "Pending",
        self::STATUS_ACCEPTED => "Accepted",
        self::STATUS_COMPLETED => "Completed",
        self::STATUS_CANCELED => "Canceled",
    );

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="notification", type="integer", nullable=true)
     */
    private $notification;

    /**
     * @var string
     *
     * @ORM\Column(name="policy", type="text", nullable=true)
     */
    private $policy;

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
     * Constructor
     */
    public function __construct()
    {
        $time = new \DateTime();
        $this->reservationDate = $time;

        $time->add(new \DateInterval(self::DATE_EXPIRATION));
        $this->expirationDate = $time->format('Y-m-d H:i');

        $this->status = self::STATUS_PENDING;
    }

    /**
     * Get statusList
     *
     * @return integer
     */
    public static function getStatusList() {
        return self::$bookingStatus;
    }

    /**
     * Get statusListInverse
     *
     * @return array
     */
    public static function getStatusListInverse() {
        $result = [];
        foreach (self::$bookingStatus as $status => $value) {
            $result[$value] = $status;
        }
        return $result;
    }

    /**
     * @return string|null
     */
    public function getStatusName()
    {
        if (!is_null($this->status)) {
            return self::$bookingStatus[$this->status];
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isAccepted()
    {
        return $this->status == self::STATUS_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status == self::STATUS_CANCELED;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set expirationDate
     *
     * @param string $expirationDate
     *
     * @return Booking
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
     * @return Booking
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
     * @param integer $status
     *
     * @return Booking
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set notification
     *
     * @param integer $notification
     *
     * @return Booking
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return integer
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set policy
     *
     * @param string $policy
     *
     * @return Booking
     */
    public function setPolicy($policy)
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * Get policy
     *
     * @return string
     */
    public function getPolicy()
    {
        return $this->policy;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Booking
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
     * @return Booking
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
}
