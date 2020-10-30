<?php

namespace App\Controller;

use App\Form\AnnonceContactType;
use App\Repository\AnnoncesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/annonces", name="annonces_")
 * @package App\Controller
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function details($slug, AnnoncesRepository $annoncesRepo,
    MailerInterface $mailer, Request $request)
    {
        $annonce = $annoncesRepo->findOneBy(['slug' => $slug]);

        if(!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce pour ce slug dans la base !!');
        }

        $form = $this->createForm(AnnonceContactType::class);
        $contact = $form->handleRequest($request);
    

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to($annonce->getUsers()->getEmail())
            ->subject('contact au sujet de votre annonce "'.$annonce->getTitle().'"')
            ->htmlTemplate('emails/contact_annonce.html.twig')
            ->context([
                'annonce' => $annonce,
                // attention email est réservé utiliser e_mai ou mail
                'mail' => $contact->get('email')->getData(),
                'message' => $contact->get('message')->getData()

            ]);
            $mailer->send($mail);
            $this->addFlash('message', 'Votre e-mail a bien été envoyé ');
            return $this->redirectToRoute('annonces_details', ['slug' => $annonce->getSlug()]);
        }

        return $this->render('annonces/details.html.twig', [
            'annonce' => $annonce,
            'form'  => $form->createView()
        ]);
    }
}
