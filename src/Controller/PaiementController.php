<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="paiement")
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
    public function checkout(CommandeRepository $cRepo): Response
    {
        $DOMAIN = 'http://127.0.0.1:8000/';

        $user = $this->getUser();
        $commande = $cRepo->findByNumeroCommande($user->getId());
        $paniers = $commande->getPaniers();
        $line_items = [];

        foreach ($paniers as $panier) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $panier->getProduit(),
                    ],
                    'unit_amount' => $panier->getProduit()->getPrix() * 100,
                ],
                'quantity' => 1,
            ];
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    $line_items,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $DOMAIN.'success',
            'cancel_url' => $DOMAIN.'cancel',
        ]);

        // dd($session);

        return $this->redirect($session->url);
    }

    /**
     * @Route("/success", name="success_paiement")
     */
    public function successUrl(): Response
    {
        return $this->render('paiement/success.html.twig', [
        ]);
    }

    /**
     * @Route("/cancel", name="cancel_paiement")
     */
    public function cancelUrl(): Response
    {
        return $this->render('paiement/cancel.html.twig', [
        ]);
    }
}
