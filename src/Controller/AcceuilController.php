<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ActualiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index(ManagerRegistry $doctrine, ActualiteRepository $actuRepo): Response
    {
        return $this->render('acceuil/index.html.twig', [
            'produits' => $doctrine->getRepository(Produit::class)->findAll(),
            'actualites' => $actuRepo->findDernieresActu(),
        ]);
    }
}
