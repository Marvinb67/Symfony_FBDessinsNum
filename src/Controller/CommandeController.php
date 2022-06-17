<?php

namespace App\Controller;

use App\Services\Panier\PanierService;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{
    private $panierServices;


    public function __construct(PanierService $panierServices )
    {
        $this->panierServices = $panierServices;
    }

    /**
     * @Route("/commande", name="commande")
     */
    public function index(ManagerRegistry $doctrine, Request $requete): Response
    {

        $panier = $this->panierServices->getPanierPlein();

        if(!$this->getUser()->getAdresses()->getValues()){
            $this->addFlash('erreur', 'Veuillez renseigner une adresse pour passer à la commande');
            
            return $this->redirectToRoute('ajouter_adresse');
        }

        return $this->render('commande/index.html.twig', [
            'panier' => $panier,
            
        ]);
    }

    public function ajouter(ManagerRegistry $doctrine, Commande $commande, Request $requete,  SessionInterface  $session)
    {
        

        $commande = new Commande();
        $commande->setUtilisateur($user);


    }
}
