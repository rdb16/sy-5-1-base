<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use App\Repository\CalendarRepository;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {       
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/data", name="users_data")
     */
    public function usersData()
    {
        return $this->render('users/data.html.twig');
    }
    /**
     * @Route("/users/data/download", name="users_data_download")
     */
    public function usersDataDownload(Request $request)
    {
        //on définit les options du pdf
        $pdfOptions = new Options();
        //la police ae défaut
        $pdfOptions->setDefaultFont('Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        //on instancie
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);
        //on génère le html
        $html = $this->renderView('users/download.html.twig');

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');

        $dompdf->render();
        //on génère un nom de fichier
        $fichier = 'user-data-'. $this->getUser()->getid() .'.pdf';

        // on envoie le pdf au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true,
        ]);

        return new Response();

    }

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request)
    {
        $annonce = new Annonces;
        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //on récupère les images transmises
            $images = $form->get('images')->getData();
            //on boucle sur les images
            foreach($images as $image) {
                //on nomme le fichier de façon unique et on le copie dans public/uploads 
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory').'/annonces',
                    $fichier
                );
                //on stocke l'image dans la base ( son nom )
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }
            

            $calcsl = (new UnicodeString($annonce->getTitle()))
                ->lower()
                ->replace(' ', '_');
            //dd($calc1);
            $annonce->setSlug($calcsl);

            $date = new \Datetime();
            $annonce->setCreatedAt($date);
            $annonce->setActive(true);
            $annonce->setUsers($this->getUser());

          //dd($annonce->getCreatedAt());

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            
            return $this->redirectToRoute('users');
        }
        
        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/users/annonces/edit/{id}", name="users_annonces_edit", methods={"GET", "POST"})
     */
    public function editAnnonce(Request $request, Annonces $annonce)
    {
        
        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);   

        

        if ($form->isSubmitted() && $form->isValid()) {

            //on récupère les nouvelles images transmises
            $images = $form->get('images')->getData();
            //on boucle sur les images
            foreach ($images as $image) {
                //on nomme le fichier de façon unique et on le copie dans public/uploads 
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory') . '/annonces',
                    $fichier
                );
                //on stocke l'image dans la base ( son nom )
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }       

            $em = $this->getDoctrine()->getManager();        
            $em->flush();

            $this->addFlash('message', 'Annonce mise à jour');

            return $this->redirectToRoute('users');
        }

        
        return $this->render('users/annonces/editAnnonce.html.twig', [
            'form' => $form->createView(), 'annonce' => $annonce
        ]);

    }



    /**
     * @Route("/users/profil/modifier", name="users_profil_modifier")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');

            return $this->redirectToRoute('users');
        }

        return $this->render('users/editprofile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/users/pass/modifier", name="users_pass_modifier")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $user= $this->getUser();

            if( $request->get('pass') == $request->get('pass2') ){

                $user->setPassword($passwordEncoder->encodePassword($user, $request->get('pass')));
                $em->flush();
                $this->addFlash('success', 'Le mot de passe a été changé avec succès');
                return $this->redirectToRoute('users');

            }else{
                    $this->addFlash('error', 'Les deux mots de passe ne sont pas identiques');
            }
            
        }

        return $this->render('users/editpass.html.twig');
    }

    /**
     * @Route("/supprime/image/{id}", name="users_annonce_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $img, Request $request) {

        $data = json_decode($request->getContent(), true);
        //on vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$img->getId(), $data['_token'])){
            $nom = $img->getName();
            //on l'efface l'image dans public/uploads
            unlink($this->getParameter('images_directory').'/annonces/'.$nom);
            //on nettoye la base
            $em= $this->getDoctrine()->getManager();
            $em->remove($img);
            $em->flush();
            //on répond en json
            return new JsonResponse(['success' => 1 ]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    /**
     * affiche le fullcalendar du user
     * @Route("/users/mycalendar", name="users_mycalendar")
     * 
     */
    public function myCalendar(CalendarRepository $calendarRepo) {
        // on ramase tous les évènements du user dans un tableau

        $events = $calendarRepo->findCalendarByUser($this->getUser());
        //dd($events);
        $rdvs = [];
        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }
        // on encode en json le tableau de tableaux
        $data = json_encode($rdvs);
        // on le passe à la vue
        return $this->render('users/mycalendar.html.twig', [
            'data' => $data
        ]);
    }
}