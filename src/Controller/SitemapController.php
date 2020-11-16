<?php

namespace App\Controller;

use App\Entity\Annonces;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_/format"="xml"})
     */
    public function index(Request $req)
    {
        //le nom de domaine
        $hostname = $req->getSchemeAndHttpHost();
        
        //on push(fin de tableau) les rls statiques
        $urls = [];
        $urls[] = ['loc' => $this->generateUrl('app_home')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('app_register')];
        
        // on push les url dynamiques
        foreach($this->getDoctrine()->getRepository(Annonces::class)->findAll() as $annonce){
            //on ne ramène que la première image de l'annonce
            /* $images = [
                'loc' => '/uploads/images/annonces/' . $annonce->getImages()[0]->getName(),
                'title' => $annonce->getTitle()
            ]; */

            //on ramène toutes les images d'une annonce
            $images = [];
            foreach($annonce->getImages() as $img){
                $images[] = [
                    'loc' => '/uploads/images/annonces/' . $img->getName(),
                    'title' => $annonce->getTitle()
                ];
            }

            $urls[] = [
                'loc' => $this->generateUrl('annonces_details', [
                    'slug' => $annonce->getSlug()
                ]),
                'images' => $images,
                'lastmod' => $annonce->getCreatedAt()->format('Y-m-d')
            ];
            //dd($urls);
        }

        //je récupère la réponse en envoyant les date au twig
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname
                // on aurait pu écrire compact('urls', 'hostname') ce qui remplace le tableau
            ])
        );

        //ajout des entêtes xml
        $response->headers->set('Content-Type','text/xml');
        //on envoie
        return $response;




        
    }
}
