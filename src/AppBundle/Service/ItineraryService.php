<?php
namespace AppBundle\Service;

use AppBundle\Entity\Content;
use AppBundle\Entity\Itinerary;
use AppBundle\Entity\User;
use AppBundle\Repository\ContentRepository;
use AppBundle\Repository\ItineraryRepository;
use Symfony\Component\Translation\TranslatorInterface;

class ItineraryService
{
    /** @var  ItineraryRepository */
    private $itineraryRepository;
    
    /** @var  ContentRepository */
    private $contentRepository;

    /** @var SendMail */
    private $sendMailService;

    /** @var TranslatorInterface */
    private $translator;

    function __construct(
        $itineraryRepository,
        $contentRepository,
        $sendMailService,
        $translator
    ) {
        $this->itineraryRepository = $itineraryRepository;
        $this->contentRepository = $contentRepository;
        $this->sendMailService = $sendMailService;
        $this->translator = $translator;
    }

    /**
     * @param Itinerary $itinerary
     * @param User|string $user
     *
     * @return Itinerary
     */
    public function isCopied(Itinerary $itinerary, $user)
    {
        if (! $user instanceof User) {
            return false;
        }
        $item = $this->itineraryRepository->findOneByUserAndParent($user,$itinerary);

        if ($item) {
            return true;
        }

        return false;
    }

    /**
     * @param Itinerary $itinerary
     * @param User|null $user
     *
     * @return Itinerary
     */
    public function copy(Itinerary $itinerary, $user = null)
    {
        $newItinerary = new Itinerary();
        $newItinerary->setTitle($itinerary->getTitle());
        $newItinerary->setDescription($itinerary->getDescription());
        $newItinerary->setImage($itinerary->getImage());
        $newItinerary->setUser($user);
        $newItinerary->setParent($itinerary);

        foreach ($itinerary->getContents() as $content) {
            $newContent = new Content();
            $newContent->setTitle($content->getTitle());
            $newContent->setDescription($content->getDescription());
            $newContent->setContentText($content->getContentText());
            $newContent->setUser($user);
            $newContent->setParent($content);
            $newContent->addItinerary($newItinerary);

            $this->contentRepository->save($newContent);
        }

        $this->itineraryRepository->save($newItinerary);

        return $newItinerary;
    }
}