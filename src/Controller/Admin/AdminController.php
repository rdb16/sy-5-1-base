<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home_")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * 
     * @Route("/stats", name="admin-stats")
     * 
     */
    public function statistiques(CategoriesRepository $categRepo, AnnoncesRepository $annRepo){
        //on ramène toutes les catégories
        $categories = $categRepo->findAll();
        $categName = [];
        $categColor = [];
        $categCount = [];

        foreach($categories as $categ){
            $categName[] = $categ->getName();
            $categColor[] = $categ->getColor();
            $categCount[] = count($categ->getAnnonces());
        }

        //on va chercher le nb d'annonce pour un jour donné
        $annonces = $annRepo->countByDate();
        //dd($annonces);
        $dates = [];
        $annoncesCount = [];
        foreach($annonces as $annonce) {
            $dates[] = $annonce['dateAnnonces'];
            $annoncesCount[] = $annonce['count'];  
        }

       return $this->render('admin/stats/stats.html.twig',[
           'categName' => json_encode($categName),
           'categColor' => json_encode($categColor),
           'categCount' => json_encode($categCount),
           'dates' => json_encode($dates),
           'annoncesCount' => json_encode($annoncesCount)
       ]);

    }
}
