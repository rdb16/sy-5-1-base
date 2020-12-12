<?php

namespace App\Controller;

use App\Repository\AgencesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class AgencesController extends AbstractController
{
    /**
     * @Route("/agences", name="app_agences")
     */
    public function index(AgencesRepository $agRepo)
    {
        $results = $agRepo->findAll();
       //mais c'est un tableau de tableau on reformate
       $agences = [];
       foreach ($results as $key => $value) {
           $agences[$key]['ville'] = $value->getNom();
           $agences[$key]['latitude'] = $value->getLatitude();
           $agences[$key]['longitude'] = $value->getLongitude();
           $agences[$key]['address'] = $value->getAddress();
       }
       //dd($agences);
       $agencesjson = json_encode($agences);
        //dd($agencesjson);
        return $this->render('agences/index.html.twig', [
            'agences' => $agencesjson
        ]);
    }
}
