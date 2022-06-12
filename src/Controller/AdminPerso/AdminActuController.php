<?php

namespace App\Controller\AdminPerso;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminActuController extends AbstractController
{
    /**
     * @Route("/admin/actu", name="app_admin_actu")
     */
    public function index(): Response
    {
        return $this->render('admin_actu/index.html.twig', [
            'controller_name' => 'AdminActuController',
        ]);
    }
}
