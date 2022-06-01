<?php

namespace App\Controller;

use App\Entity\Actualite;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $actualites = $doctrine->getRepository(Actualite::class)->findBy([], ['poster_le' => 'DESC']);

        return $this->render('acceuil/index.html.twig', [
            'actualites' => $actualites,
        ]);
    }
}
