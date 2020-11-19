<?php

namespace App\Controller;

use DateTime;
use App\Entity\Calendar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiCalendarController extends AbstractController
{
    /**
     * @Route("/api/calendar", name="api_calendar")
     */
    public function index()
    {
        return $this->render('api_calendar/index.html.twig', [
            'controller_name' => 'ApiCalendarController',
        ]);
    }

    /**
     *
     * @Route("/api/calendar/{id}/edit", name="api_calendar_event_edit", methods={"PUT"})
     */
    public function majEvent(?Calendar $calendar, Request $req)
    {
        // on récupère le json envoyé par ajax
        $donnees = json_decode($req->getContent());
        // on fait une vérif sur les datas rçues       
        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor) 
           
        ){
            //les données sont complètes, on initialise un 200 => c'est ok
            $code = 200;
            //on vérifie si l'id existe, si oui => maj si non création
            if(!$calendar){
                $calendar = new Calendar;
                $code = 201;// created                         
            }
            //on hydrate l'objet avec nos datas
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new DateTime($donnees->start));
            if($donnees->allDay){//si allDay est à true
                $calendar->setEnd(new DateTime($donnees->start));
            }else{
                $calendar->setEnd(new DateTime($donnees->end));
            }
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);
            $calendar->setUserCalendar($this->getUser());                  
            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();    
            return new Response('données ok ', $code);

        } else {
            // données incomplètes
            return new Response('données incomplètes' , 404);
        }        
    }
}
