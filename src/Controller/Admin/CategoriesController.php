<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller\Admin
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $catsRepo)
    {
      return $this->render('admin/categories/index.html.twig', [
          'categories' => $catsRepo->findAll()
      ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutCategories(Request $request)
    {
        $category = new Categories;

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
