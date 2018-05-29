<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Movie;

class Slugifier 
{
    private $slugger;

    public function __construct($slugger)
    {
        $this->slugger = $slugger;
    } 

    /**
     * Le nom des fonction correspond aux event sur lequel on souhaite appliquer 
     * une action
     */
    public function prePersist(LifecycleEventArgs $eventArgs){
        $this->setSlug($eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs){
        $this->setSlug($eventArgs);
    }

    private function setSlug($eventArgs){
        $entity = $eventArgs->getEntity();

        if(!$entity instanceof Movie){
            return;
        }

        $sluggedTitle = $this->slugger->slugify($entity->getTitle());

        $entity->setSlug($sluggedTitle);
    }
}
