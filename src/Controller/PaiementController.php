<?php

namespace App\Controller;

use App\Entity\Commande;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="app_paiement")
     */
    public function index(): Response
    {
        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(): Response
    {
        $DOMAIN = 'http://127.0.0.1:8000/';
        $commandes = $doctrine->getRepository(Commande::class)->findAll();

        $user = $this->getUser();

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $session = Session::create([
            'payement_method_types' => ['card'],
            'line_items' => [
                [
                    'price-data' => [
                        'currency' => 'euro',
                        'product_data' => [
                            'name' => 'Nounours',
                        ],
                        'unit_amount' => 15,
                    ],
                    'quantity' => 2,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'a faire',
            'cancel_url' => 'a faire',
        ]);
        // foreach ($paniers as $produit) {
        //     dd($produit->getProduit());
        // }

        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }
}
