<?php

namespace App\Controller\Admin;

use App\Entity\Agences;
use App\Form\AgencesType;
use App\Repository\AgencesRepository;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/agences", name="admin_agences_")
 * @package App\Controller\Admin
 */
class AgencesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AgencesRepository $agenciesRepo)
    {
      return $this->render('admin/agences/index.html.twig', [
          'agences' => $agenciesRepo->findAll()
      ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutAgence(Request $request)
    {
        $agency = new Agences;

        $form = $this->createForm(AgencesType::class, $agency);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            //$agency->setNom();
            //dd($category->getslug());
            $em = $this->getDoctrine()->getManager();
            $em->persist($agency);
            $em->flush(); 
            return $this->redirectToRoute('admin_agences_home');
        }

        
        return $this->render('admin/agences/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierCategories(categories $category, Request $request)
    {

        $form = $this->createForm(CategoriesType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $calcsl = (new UnicodeString($category->getName()))
            ->lower()
            ->replace(' ','_');
            //dd($calc1);
            $category->setSlug($calcsl);
            //dd($category->getslug());
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush(); 
            return $this->redirectToRoute('admin_categories_home');
        }

        
        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
}
