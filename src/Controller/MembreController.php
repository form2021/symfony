<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// ne pas oublier les use
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class MembreController extends AbstractController
{
    #[Route('/membre', name: 'membre', methods: ['GET', 'POST'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, SluggerInterface $slugger): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        $userConnecte = $this->getUser();
        $messageConfirmation = "";
        if ($form->isSubmitted() && $form->isValid()) {
            // compléter les infos manquantes
            $annonce->setDatePublication(new \DateTime());
            // https://symfony.com/doc/current/security.html#a-fetching-the-user-object
            // ajouter l'auteur de l'annonce avec l'utilisateur connecté
            $annonce->setUser($userConnecte);
            
            // code de gestion de upload image
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),    // dossier cible
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $annonce->setImage($newFilename);
            }
            else {
                $annonce->setImage("");     // aucun fichier uploade
            }

            // code qui insère la nouvelle ligne dans SQL
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            $messageConfirmation = "votre annonce est publiée";
        }

        // après le traitement du create pour obtenir la liste à jour
        $annonces = $annonceRepository->findBy([
            "user" => $userConnecte,            
            // on filtre les lignes pour obtenir seulement les annonces de l'utilisateur
        ], [ "datePublication" => "DESC"]);

        return $this->render('membre/index.html.twig', [
            'annonces' => $annonces,    // LISTE DES ANNONCES
            'annonce' => $annonce,
            'form' => $form->createView(),
            'messageConfirmation' => $messageConfirmation,
        ]);
    }

    #[Route('/{id}', name: 'membre_annonce_delete', methods: ['DELETE'])]
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            // verifier que l'annonce appartient à l'utilisateur connecté
            $userConnecte = $this->getUser();
            $auteurAnnonce = $annonce->getUser();
            if ($userConnecte != null && $auteurAnnonce != null) {
                if ($userConnecte->getId() == $auteurAnnonce->getId()) {
                    // declenche le delete de la ligne
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($annonce);
                    $entityManager->flush();

                    // il faudrait aussi supprimer le fichier image
                    // https://www.php.net/manual/fr/function.unlink.php
                    $dossierUpload = $this->getParameter('images_directory');
        
                    $fichierImage = "$dossierUpload/" . $annonce->getImage();
                    if (is_file($fichierImage)) {
                        unlink($fichierImage);
                    }
                }
            }
        }

        return $this->redirectToRoute('membre');
    }

}
