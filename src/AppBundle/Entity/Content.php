<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentRepository")
 */
class Content
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastModificationDate;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique_base="userSlug", unique=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="userSlug", type="string", length=255, nullable=true)
     */
    private $userSlug;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contentText;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $demo;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="contents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Itinerary", inversedBy="contents", cascade={"persist"})
     * @ORM\JoinTable(name="content_itineraries",
     *    joinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")},
     *    inverseJoinColumns={@ORM\JoinColumn(name="itinerary_id", referencedColumnName="id")}
     * )
     */
    private $itineraries;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;


    public function __construct()
    {
        $this->itineraries = new ArrayCollection();
        $this->children = new ArrayCollection();

        $now = new DateTime("now");
        $this->setCreationDate($now);
        $this->setLastModificationDate();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setSlug(null);
            $this->setUser(null);
            $this->setCreationDate(new DateTime("now"));
        }
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Content
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set lastModificationDate
     *
     * @return Content
     */
    public function setLastModificationDate()
    {
        $this->lastModificationDate = new DateTime("now");;

        return $this;
    }

    /**
     * Get lastModificationDate
     *
     * @return \DateTime
     */
    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Content
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
     * Set title
     *
     * @param string $title
     *
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Content
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
     * Set image
     *
     * @param string $image
     *
     * @return Content
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add itinerary
     *
     * @param \AppBundle\Entity\Itinerary $itinerary
     *
     * @return Content
     */
    public function addItinerary(Itinerary $itinerary)
    {
        $this->itineraries->add($itinerary);

        return $this;
    }

    /**
     * Remove itinerary
     *
     * @param \AppBundle\Entity\Itinerary $itinerary
     */
    public function removeItinerary(Itinerary $itinerary)
    {
        $this->itineraries->removeElement($itinerary);
    }

    /**
     * Get itineraries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItineraries()
    {
        return $this->itineraries;
    }

    /**
     * Set contentText
     *
     * @param string $contentText
     *
     * @return Content
     */
    public function setContentText($contentText)
    {
        $this->contentText = $contentText;

        return $this;
    }

    /**
     * Get contentText
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Content
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set demo
     *
     * @param boolean $demo
     *
     * @return Content
     */
    public function setDemo($demo)
    {
        $this->demo = $demo;

        return $this;
    }

    /**
     * Get demo
     *
     * @return boolean
     */
    public function getDemo()
    {
        return $this->demo;
    }

    /**
     * Get demo
     *
     * @return boolean
     */
    public function isDemo()
    {
        return $this->demo;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Content $child
     *
     * @return Content
     */
    public function addChild(\AppBundle\Entity\Content $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Content $child
     */
    public function removeChild(\AppBundle\Entity\Content $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Content $parent
     *
     * @return Content
     */
    public function setParent(\AppBundle\Entity\Content $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Content
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set userSlug
     *
     * @param string $userSlug
     *
     * @return Content
     */
    public function setUserSlug($userSlug)
    {
        $this->userSlug = $this->getUser()->getUsername();

        return $this;
    }

    /**
     * Get userSlug
     *
     * @return string
     */
    public function getUserSlug()
    {
        return $this->userSlug;
    }
}
