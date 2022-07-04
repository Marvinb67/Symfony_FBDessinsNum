<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\ActualiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index(ProduitRepository $produitRepo, ActualiteRepository $actuRepo): Response
    {
        return $this->render('acceuil/index.html.twig', [
            'produits' => $produitRepo->derniersProduits(),
            'actualites' => $actuRepo->findDernieresActu(),
        ]);
    }
}
