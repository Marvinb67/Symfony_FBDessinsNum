<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
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
    public function index(CommandeRepository $cRepo): Response
    {
        $user = $this->getUser();
        $commande = $cRepo->findByNumeroCommande($user->getId());
        dump($commande);
        $paniers = $commande->getPaniers();

        return $this->render('paiement/index.html.twig', [
            'commande' => $commande,
            'paniers' => $paniers,
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

        if ($user != $commande->getUtilisateur()) {
            return $this->redirectToRoute('accueil');
        }

        $commande->setPayer(true);
        $facture = new Facture();
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
            'commande' => $commande,
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
     * @Route("/facture/{id}", name="facture")
     */
    public function telechargerFacture(Commande $commande)
    {

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'courier');

        $dompdf = new Dompdf($options);

        $html = $this->renderView('paiement/facture.html.twig', [
            'sitename' => 'Test facture pdf',
            'commande' => $commande,
        ]);

        $dompdf->setBasePath('laragon\www\Marvin\Symfony_FBDessinsNum\public');
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream('mypdf.pdf', [
            'Attachment' => false,
        ]);

        exit(0);
    }
}
