<?php
namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $isMaintenance;

    public function __construct()
    {
        $this->isMaintenance = true;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {

        if(!$this->isMaintenance){
            return;
        }

        $response = $event->getResponse();

        //je recupere le contenu de ma page soit mon HTML
        $content = $response->getContent();

        $maintenanceBanner = '<div class="container mt-3">
        <div class="alert alert-danger">
            Maintenance prévue du 18 mai 00:00 au 19 mai 06:00 inclus 
        </div>
        </div>';

        $content = str_replace ('<body>', '<body>'. $maintenanceBanner, $content);

        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse'
        );
    }
}