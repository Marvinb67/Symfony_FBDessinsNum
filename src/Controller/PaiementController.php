<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Facture;
use Stripe\Checkout\Session;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
                'quantity' => $panier->getQuantite(),
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
    public function successUrl(CommandeRepository $cRepo, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $this->getUser();
        $commande = $cRepo->findByNumeroCommande($user->getId());

        if($user != $commande->getUtilisateur()){
            return $this->redirectToRoute('accueil');
        }

        $commande->setPayer(true);
        $facture = new Facture;
        $facture->setNumero($facture->genererNum());
        $facture->setNom($user->getNom());
        $facture->setPrenom($user->getPrenom());
        $facture->setCommande($commande);
        $facture->setAdresse($commande->getAdresseLivraison());
        $facture->setCp($commande->getAdresseLivraison()->getCp());
        $facture->setVille($commande->getAdresseLivraison()->getVille());
        $facture->setPays($commande->getAdresseLivraison()->getPays());
        $em->persist($facture);
        $em->flush();

        return $this->render('paiement/success.html.twig', [
            'facture' => $facture,
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

     /**
     * @Route("/facture", name="facture")
     */
    public function facture(Facture $facture)
    {
        dump($facture);
        return $this->render('paiement/facture.html.twig', [
            'facture' => $facture,
        ]);
    }
}
