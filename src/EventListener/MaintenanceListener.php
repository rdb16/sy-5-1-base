<?php
namespace App\EventListener;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MaintenanceListener 
{
    private $maintenance;
    private $twig;

    public function __construct($maintenance, Environment $twig){
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }
    
    public function onKernelRequest(RequestEvent $event){
        //on vérifie si le fichier .maintenance n'existe pas
        if(!file_exists($this->maintenance)){
            return;
        }

        // on définit la réponscar le fichier exitse
        //dd($this->maintenance);
        $event->setResponse(
            new Response(
                $this->twig->render('maintenance/maintenance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            )
        );
        // on stoppe le traitement des évènements
        $event->stopPropagation();
    }
    
}
