<?php
namespace AppBundle\Entity;

use Avoo\AchievementBundle\Model\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\File(maxSize="2048k")
     * @Assert\Image(mimeTypesMessage="Please upload a valid image.")
     */
    protected $profilePictureFile;

    // for temporary storage
    private $tempProfilePicturePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profilePicturePath;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    protected $paypal;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $sponsorshipAccount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $premiumAccount;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserAchievement", mappedBy="user")
     */
    protected $achievements;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Itinerary", mappedBy="user")
     */
    protected $itineraries;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Content", mappedBy="user")
     */
    protected $contents;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function __toString()
    {
        if ($this->username != '')
            return (string) $this->username;
        else
            return (string) $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getAchievements()
    {
        return $this->achievements;
    }

    /**
     * Sets the file used for profile picture uploads
     *
     * @param UploadedFile $file
     * @return object
     */
    public function setProfilePictureFile(UploadedFile $file = null)
    {
        // set the value of the holder
        $this->profilePictureFile = $file;
        // check if we have an old image path
        if (isset($this->profilePicturePath)) {
            // store the old name to delete after the update
            $this->tempProfilePicturePath = $this->profilePicturePath;
            $this->profilePicturePath = null;
        } else {
            $this->profilePicturePath = 'initial';
        }

        return $this;
    }

    /**
     * Get the file used for profile picture uploads
     *
     * @return UploadedFile
     */
    public function getProfilePictureFile()
    {
        return $this->profilePictureFile;
    }

    /**
     * Set profilePicturePath
     *
     * @param string $profilePicturePath
     * @return User
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get profilePicturePath
     *
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * Get the absolute path of the profilePicturePath
     */
    public function getProfilePictureAbsolutePath()
    {
        return null === $this->profilePicturePath
            ? null
            : $this->getUploadRootDir() . '/' . $this->profilePicturePath;
    }

    /**
     * Get root directory for file uploads
     *
     * @return string
     */
    protected function getUploadRootDir($type = 'profilePicture')
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../web/' . $this->getUploadDir($type);
    }

    /**
     * Specifies where in the /web directory profile pic uploads are stored
     *
     * @return string
     */
    protected function getUploadDir($type = 'profilePicture')
    {
        // the type param is to change these methods at a later date for more file uploads
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/user/profilepics';
    }

    /**
     * Get the web path for the user
     *
     * @return string
     */
    public function getWebProfilePicturePath()
    {
        return '/' . $this->getUploadDir() . '/' . $this->getProfilePicturePath();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadProfilePicture()
    {
        if (null !== $this->getProfilePictureFile()) {
            // a file was uploaded
            // generate a unique filename
            $filename = $this->generateRandomProfilePictureFilename();
            $this->setProfilePicturePath($filename . '.' . $this->getProfilePictureFile()->guessExtension());
        }
    }

    /**
     * Generates a 32 char long random filename
     *
     * @return string
     */
    public function generateRandomProfilePictureFilename()
    {
        $count = 0;
        do {
            $random = random_bytes(16);
            $randomString = bin2hex($random);
            $count++;
        } while (file_exists($this->getUploadRootDir() . '/' . $randomString . '.' . $this->getProfilePictureFile()->guessExtension()) && $count < 50);

        return $randomString;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     *
     * Upload the profile picture
     *
     * @return mixed
     */
    public function uploadProfilePicture()
    {
        // check there is a profile pic to upload
        if ($this->getProfilePictureFile() === null) {
            return;
        }
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getProfilePictureFile()->move($this->getUploadRootDir(),
            $this->getProfilePicturePath());

        // check if we have an old image
        if (isset($this->tempProfilePicturePath) && file_exists($this->getUploadRootDir() . '/' . $this->tempProfilePicturePath)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->tempProfilePicturePath);
            // clear the temp image path
            $this->tempProfilePicturePath = null;
        }
        $this->profilePictureFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeProfilePictureFile()
    {
        $file = $this->getProfilePictureAbsolutePath();
        if ($file && file_exists($this->getProfilePictureAbsolutePath())) {
            unlink($file);
        }
    }

    /**
     * Add achievement
     *
     * @param \AppBundle\Entity\UserAchievement $achievement
     *
     * @return User
     */
    public function addAchievement(\AppBundle\Entity\UserAchievement $achievement)
    {
        $this->achievements[] = $achievement;

        return $this;
    }

    /**
     * Remove achievement
     *
     * @param \AppBundle\Entity\UserAchievement $achievement
     */
    public function removeAchievement(\AppBundle\Entity\UserAchievement $achievement)
    {
        $this->achievements->removeElement($achievement);
    }

    /**
     * Set sponsorshipAccount
     *
     * @param boolean $sponsorshipAccount
     *
     * @return User
     */
    public function setSponsorshipAccount($sponsorshipAccount)
    {
        $this->sponsorshipAccount = $sponsorshipAccount;

        return $this;
    }

    /**
     * Get sponsorshipAccount
     *
     * @return boolean
     */
    public function getSponsorshipAccount()
    {
        return $this->sponsorshipAccount;
    }

    /**
     * Set premiumAccount
     *
     * @param boolean $premiumAccount
     *
     * @return User
     */
    public function setPremiumAccount($premiumAccount)
    {
        $this->premiumAccount = $premiumAccount;

        return $this;
    }

    /**
     * Get premiumAccount
     *
     * @return boolean
     */
    public function getPremiumAccount()
    {
        return $this->premiumAccount;
    }

    /**
     * Set itineraries
     *
     * @param \AppBundle\Entity\Itinerary $itineraries
     *
     * @return User
     */
    public function setItineraries(\AppBundle\Entity\Itinerary $itineraries = null)
    {
        $this->itineraries = $itineraries;

        return $this;
    }

    /**
     * Get itineraries
     *
     * @return \AppBundle\Entity\Itinerary
     */
    public function getItineraries()
    {
        return $this->itineraries;
    }

    /**
     * Add itinerary
     *
     * @param \AppBundle\Entity\Itinerary $itinerary
     *
     * @return User
     */
    public function addItinerary(\AppBundle\Entity\Itinerary $itinerary)
    {
        $this->itineraries[] = $itinerary;

        return $this;
    }

    /**
     * Remove itinerary
     *
     * @param \AppBundle\Entity\Itinerary $itinerary
     */
    public function removeItinerary(\AppBundle\Entity\Itinerary $itinerary)
    {
        $this->itineraries->removeElement($itinerary);
    }

    /**
     * Add content
     *
     * @param \AppBundle\Entity\Content $content
     *
     * @return User
     */
    public function addContent(\AppBundle\Entity\Content $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContent(\AppBundle\Entity\Content $content)
    {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set paypal
     *
     * @param string $paypal
     *
     * @return User
     */
    public function setPaypal($paypal)
    {
        $this->paypal = $paypal;

        return $this;
    }

    /**
     * Get paypal
     *
     * @return string
     */
    public function getPaypal()
    {
        return $this->paypal;
    }
}
