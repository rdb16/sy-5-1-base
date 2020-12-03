<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\SearchAnnonceType;
use App\Repository\AnnoncesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AnnoncesRepository $annoncesRepo, Request $request, PaginatorInterface $paginator)
    {
        //$annonces = $annoncesRepo->findBy(['active' =>  true], ['created_at' => 'desc'], 5);
        $annonces = $annoncesRepo->findBy(['active' =>  true], ['created_at' => 'desc']);

        $form = $this->createForm(SearchAnnonceType::class);

        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //on lance la recherche
            //dd($search->get('mots')->getData());
            //dd($search->get('mots'));
            //dd($annoncesRepo->search("-bis"));
            $annonces = $annoncesRepo->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData()
                );
            //dd($annonces);
        }

        
        $paginatedAnnonces = $paginator->paginate(
            //la liste à paginer
            $annonces,
            //num de la page encours dans l'url sinon 1
            $request->query->getInt('page', 1),
            
            3 //nb de resultats par page
        );
        // on essaye de customiser la sortie
        $paginatedAnnonces->setCustomParameters([
            'align' => 'center',
            'size' => 'large',
            'style' => 'bottom',
            'rounded' => true,
        ]);

        return $this->render('main/index.html.twig', [
            'paginatedAnnonces' => $paginatedAnnonces,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mentions/legales", name="mentions")
     */
     public function mentions()
     {
         return $this->render('main/mentions.html.twig');
     }

    /**
     * @Route("/contact", name="contact"), methods="{GET,POST}
     */
    public function contact(Request $req, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $contact = $form->handleRequest($req);
        
        if( $form->isSubmitted() && $form->isValid()){
            $envoi =   (new TemplatedEmail())
                        ->from($contact->get('email')->getdata())
                        ->to('0607514708@free.fr')
                        ->subject($contact->get('sujet')->getData())
                        ->htmlTemplate('emails/contact.html.twig')
                        ->context([
                            'mail' => $contact->get('email')->getData(),
                            'sujet' => $contact->get('sujet')->getData(),
                            'message' => $contact->get('message')->getData(),
                            
                        ])
            ;

            $mailer->send($envoi);
            $this->addFlash('message', 'votre mail a étét bien envoyé');
            return $this->redirectToRoute('app_home');
        }


        
        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
