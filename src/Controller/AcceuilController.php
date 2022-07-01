<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index(ProduitRepository $produits): Response
    {
        return $this->render('acceuil/index.html.twig', [
            'produits' => $produits->derniersProduits(),
        ]);
    }
}
