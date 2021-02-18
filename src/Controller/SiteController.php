<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// ne pas oublier de rajouter les lignes use...
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\AnnonceRepository;
use App\Entity\Annonce;

class SiteController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $messageConfirmation    = 'merci de remplir le formulaire';
        $classConfirmation      = 'gris';

        $newsletter = new Newsletter(); // code créé avec le make:entity
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        // bloc if pour le traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // alors on traite le formulaire

            // ici on peut compléter les infos manquantes
            $objetDate = new \DateTime();   // objet qui contient la date actuelle
            $newsletter->setDateInscription($objetDate);
    
            // on envoie les infos en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newsletter);
            $entityManager->flush();

            // tout s'est bien passé
            $messageConfirmation    = 'merci de votre inscription';
            $classConfirmation      = 'vert';

            // pas de redirection pour la page d'accueil
            // return $this->redirectToRoute('newsletter_index');
        }

        return $this->render('site/index.html.twig', [
            'classConfirmation'     => $classConfirmation,
            'messageConfirmation'   => $messageConfirmation,    // tuyau de transmission entre PHP et twig
            'newsletter' => $newsletter,
            'form' => $form->createView(),
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/galerie', name: 'galerie')]
    public function galerie(): Response
    {
        return $this->render('site/galerie.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('site/contact.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    #[Route('/annonces', name: 'annonces', methods: ['GET'])]
    public function annonces(AnnonceRepository $annonceRepository): Response
    {
        // https://symfony.com/doc/current/doctrine.html#fetching-objects-from-the-database
        // $annonces = $annonceRepository->findAll();   // TROP BASIQUE CAR TRIE PAR id CROISSANT
        $annonces = $annonceRepository->findBy([], [ "datePublication" => "DESC"]);

        return $this->render('site/annonces.html.twig', [
            'annonces' => $annonces,    // SELECT * FROM annonces
        ]);
    }

    #[Route('/annonce/{slug}/{id}', name: 'annonce', methods: ['GET'])]
    public function annonce(Annonce $annonce): Response
    {
        // méthode pour afficher une seule annonce
        return $this->render('site/annonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/recherche', name: 'recherche')]
    public function recherche(Request $request, AnnonceRepository $annonceRepository): Response
    {
        // récupérer le mot recherché
        // et lancer une requête SQL pour chercher les annonces 
        // dont le titre contient le mot clé
        $mot = $request->get('mot');
        // DEBUG
        dump($mot);
        if (!empty($mot)) {
            // on va lancer la recherche sur le mot
            // recherche exacte
            // $annonces = $annonceRepository->findBy([
            //     "titre" => $mot,
            // ], [ "datePublication" => "DESC"]);

            // si on veut que le titre puisse avoir un texte en plus que le mot cherché
            // => SQL LIKE 
            // https://sql.sh/cours/where/like
            $annonces = $annonceRepository->chercherMot($mot);

        }
        

        return $this->render('site/recherche.html.twig', [
            'mot'      => $mot,
            'annonces' => $annonces ?? [],
        ]);
    }

}
