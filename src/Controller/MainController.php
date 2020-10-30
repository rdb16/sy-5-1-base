<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
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
    public function index(AnnoncesRepository $annoncesRepo)
    {
        return $this->render('main/index.html.twig', [
            'annonces' => $annoncesRepo->findBy(['active' =>  true],
             ['created_at' => 'desc']),
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
