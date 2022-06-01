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
    public function show(Actualite $actualite, ManagerRegistry $doctrine, Request $requete): Response
    {
        $em = $doctrine->getManager();
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire = $form->getData();
            $commentaire->setUtilisateur($this->getUser());
            $commentaire->setActualite($actualite);

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('show_actualite', [
                'id' => $actualite->getId(),
            ]);
        }

        $commentaire_list = $actualite->getCommentaires();

        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
            'formCommentaire' => $form->createView(),
            'commentaire_list' => $commentaire_list,
        ]);
    }
}
