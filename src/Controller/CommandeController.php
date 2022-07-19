<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\CommandeType;
use App\Services\Panier\PanierService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private $panierServices;

    public function __construct(PanierService $panierServices)
    {
        $this->panierServices = $panierServices;
    }

    /**
     * @Route("/commande", name="commande")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $commandes = $user->getCommandes();

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/commande/validation", name="validation_commande")
     */
    public function validationCommande(ManagerRegistry $doctrine, Request $requete): Response
    {
        $em = $doctrine->getManager();
        $session = $requete->getSession();
        $panier = $session->get('panier');

        if (!$this->getUser()->getAdresses()->getValues()) {
            $this->redirectToRoute('ajouter_adresse');
        }
        if (!$panier) {
            return $this->redirectToRoute('index_boutique');
        }

        foreach (array_keys($session->get('panier')) as $prod) {
            $produits[] = $em->getRepository(Produit::class)->find($prod);
        }
        $total = $this->panierServices->getTotal();
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($produits as $article) {
                $cmdPanier = new Panier();
                $cmdPanier->setQuantite($panier[$article->getId()]);
                $cmdPanier->setProduit($article);
                $cmdPanier->setCommande($commande);
                $em->persist($cmdPanier);
            }
            $commande->setPrixTotal($total);
            $commande->setNomComplet($form->get('nomComplet')->getData());
            $commande->setAdresseLivraison($form->get('adresseLivraison')->getData());
            $commande->setUtilisateur($this->getUser());
            $commande->setPayer(false);
            $em->persist($commande);
            $em->flush();
            $session->remove('panier');

            return $this->redirectToRoute('paiement');
        }

        return $this->render('commande/validation.html.twig', [
            'formCommande' => $form->createView(),
            'total' => $total,
            'panier' => $this->panierServices->getPanierPlein(),
        ]);
    }
}
