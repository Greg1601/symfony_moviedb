<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Twig\Environment as Twig;

use AppBundle\Controller\MovieController;
use AppBundle\Entity\Movie;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $doctrine;
    private $twig;

    public function __construct(Doctrine $doctrine, Twig $twig)
    {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controllerAnMethod = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controllerAnMethod)) {
            return;
        }

                
        //on recupere la methode et le controller courant
        $controller = $controllerAnMethod[0];
        $method = $controllerAnMethod[1];

        //teste si l'objet controller est bien du type MovieController
        // et ne va afficher que le message random sur les pages front soit MovieController par exemple
        if ($controller instanceof MovieController && ($method === 'indexAction')) {
            return;
        }

        $movies = $this->doctrine->getRepository(Movie::class)->findAll();

        $randomKey = array_rand($movies);

        $this->twig->addGlobal('randomMovie', $movies[$randomKey]);
    }

    public static function getSubscribedEvents()
    {
        return array(
            /*
                Note: on mappe l'envent kernel.controller alias KernelEvents::CONTROLLER
                sur la fonction souhaité ici onKernelController mais possibilité de changer le nom
                si tel est le cas changer le nom de la fonction appelée aussi
                
            */
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}