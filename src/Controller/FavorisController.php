<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris", name="favoris")
     */
    public function index(): Response
    {
        $favoris = $this->getUser()->getFavoris();

        return $this->render('favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }

    /**
     * @Route("/favoris/ajouter/{id}", name="ajout_favoris")
     */
    public function ajoutFavoris(ManagerRegistry $doctrine, Produit $produit): Response
    {
        $em = $doctrine->getManager();
        $this->getUser()->addFavori($produit);

        $em->flush();

        return $this->redirectToRoute('index_boutique');
    }

    /**
     * @Route("/favoris/retirer/{id}", name="retirer_favoris")
     */
    public function retirer(ManagerRegistry $doctrine, Produit $produit)
    {
        $this->getUser()->removeFavori($produit);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('favoris');
    }
}
