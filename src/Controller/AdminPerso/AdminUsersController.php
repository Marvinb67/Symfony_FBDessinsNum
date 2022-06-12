<?php

namespace App\Controller\AdminPerso;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $utilisateurs = $doctrine->getRepository(Utilisateur::class)->findBy([], ['nom' => 'ASC']);

        return $this->render('admin/admin_users/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
}
