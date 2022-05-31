<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualiteController extends AbstractController
{
    /**
     * @Route("/actualite", name="actualite")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $actualites = $doctrine->getRepository(Actualite::class)->findBy([], ['poster_le' => 'DESC']);

        return $this->render('actualite/index.html.twig', [
            'actualites' => $actualites,
        ]);
    }

    /**
     * @Route("/actualite/{id}", name="show_actualite")
     */
    public function show(Actualite $actualite, ManagerRegistry $doctrine, Commentaire $commentaire = null, Request $requete): Response
    {
        $em = $doctrine->getManager();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('show_actualite');
        }

        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
            'formCommentaire' => $form->createView(),
        ]);
    }

    // public function commentaire(ManagerRegistry $doctrine, Commentaire $commentaire = null, Request $requete)
    // {
    //     $em = $doctrine->getManager();

    //     $form = $this->createForm(CommentaireType::class, $commentaire);

    //     $form->handleRequest($requete);

    //     $em->persist($commentaire);
    //     $em->flush();

    //     return $this->redirectToRoute('show_actualite');

    //     return $this->render('acualite/.html.twig', [
    //         'formProduit' => $form->createView(),
    //     ]);
    // }
}
