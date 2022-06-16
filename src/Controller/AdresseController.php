<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{
    /**
     * @Route("/adresse", name="app_adresse")
     */
    public function index(AdresseRepository $adresses): Response
    {
        return $this->render('adresse/index.html.twig', [
            'adresses' => $adresses->findAll(),
        ]);
    }

    public function ajouter()
    {
    }
}
