<?php
namespace AppBundle\Entity;

use Avoo\AchievementBundle\Entity\UserAchievement as BaseUserAchievement;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Avoo\AchievementBundle\Repository\UserAchievementRepository")
 * @ORM\Table(name="user_achievement")
 * @ORM\HasLifecycleCallbacks()
 */
class UserAchievement extends BaseUserAchievement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="achievements")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
}
