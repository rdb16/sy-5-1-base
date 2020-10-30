<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
}