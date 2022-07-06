<?php

namespace App\Controller\AdminPerso;

use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommandeController extends AbstractController
{
    /**
     * @Route("/admin/commande", name="admin_commande")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        return $this->render('admin/admin_commande/index.html.twig', [
            'commandes' => $doctrine->getRepository(Commande::class)->findAll(),
        ]);
    }
}
