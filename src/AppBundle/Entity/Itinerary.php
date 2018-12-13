<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="itinerary")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItineraryRepository")
 */
class Itinerary
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-", unique=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     */
    private $userSlug;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $paid;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    protected $price;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="itineraries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Content", mappedBy="itineraries", cascade={"persist"})
     */
    protected $contents;

    /**
     * @ORM\OneToMany(targetEntity="Itinerary", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Itinerary", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;


    public function __construct()
    {
        $this->contents = new ArrayCollection();
        $this->children = new ArrayCollection();

        $now = new DateTime("now");
        $this->setCreationDate($now);
        $this->setLastModificationDate($now);
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

//            foreach ($this->contents as $content) {
//                $newContent = clone $content;
//                $this->addContent($newContent);
//            }
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
     * @return Itinerary
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
     * @return Itinerary
     */
    public function setLastModificationDate($date)
    {
        $this->lastModificationDate = $date;

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
     * @return Itinerary
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
     * @return Itinerary
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
     * @return Itinerary
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
     * @return Itinerary
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
     * Add content
     *
     * @param \AppBundle\Entity\Content $content
     *
     * @return Itinerary
     */
    public function addContent(Content $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContent(Content $content)
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Itinerary
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
     * Get private
     *
     * @return boolean
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Itinerary
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Itinerary $child
     *
     * @return Itinerary
     */
    public function addChild(\AppBundle\Entity\Itinerary $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Itinerary $child
     */
    public function removeChild(\AppBundle\Entity\Itinerary $child)
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
     * @param \AppBundle\Entity\Itinerary $parent
     *
     * @return Itinerary
     */
    public function setParent(\AppBundle\Entity\Itinerary $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Itinerary
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

    /**
     * Set paid
     *
     * @param boolean $paid
     *
     * @return Itinerary
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }
}
