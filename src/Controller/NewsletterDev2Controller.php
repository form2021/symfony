<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/newsletter')]
class NewsletterDev2Controller extends AbstractController
{
    #[Route('/', name: 'newsletter_index', methods: ['GET'])]
    public function index(NewsletterRepository $newsletterRepository): Response
    {
        // ICI ON VA AJOUTER NOTRE CODE PHP EN ORIENTE OBJET
        // exemple
        $toto = "BONNE ANNEE DU BUFFLE DE METAL";

        return $this->render('newsletter/index.html.twig', [
            // cle        => valeur
            // variable twig
            'toto'        => $toto,
            'newsletters' => $newsletterRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'newsletter_show', methods: ['GET'])]
    public function show(Newsletter $newsletter): Response
    {
        return $this->render('newsletter/show.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

}
