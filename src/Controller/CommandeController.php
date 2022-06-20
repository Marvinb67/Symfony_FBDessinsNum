<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Services\Panier\PanierService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(ManagerRegistry $doctrine, Request $requete): Response
    {
        $panier = $this->panierServices->getPanierPlein();
        $total = $this->panierServices->getTotal();

        if (!$this->getUser()->getAdresses()->getValues()) {
            $this->addFlash('erreur', 'Veuillez renseigner une adresse pour passer à la commande');

            return $this->redirectToRoute('ajouter_adresse');
        }

        $form = $this->createForm(CommandeType::class);
        $form->handleRequest($requete);

        return $this->render('commande/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
            'formCommande' => $form->createView(),
        ]);
    }

    /**
     * @Route("/commande/validation", name="validation_commande")
     */
    public function ajouter(Request $requete, ManagerRegistry $doctrine, PanierService $panierService)
    {
        $em = $doctrine->getManager();
        $session = $requete->getSession();

        if (!$session->has('panier')) {
            return $this->redirectToRoute('index_boutique');
        }
        $commande = new Commande();

        $panier = $session->get('panier');
        // $produits = $em->getRepository(Produit::class)->findBy(array_keys($panier));
        foreach (array_keys($session->get('panier')) as $prod) {
            $produits[] = $em->getRepository(Produit::class)->find($prod);
        }
        $panierCmd = [];
        $total = $panierService->getTotal();
        foreach ($produits as $article) {
            $panierCmd[$article->getId()] = [
                'nom' => $article->getNom(),
                'quantite' => $panier[$article->getId()],
            ];
            $cmdArticle = new Panier();
            $cmdArticle->setQuantite($panier[$article->getId()]);
            $cmdArticle->setProduit($article);
            $cmdArticle->setCommande($commande);
            $em->persist($cmdArticle);
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($requete);

        if($form->isSubmitted() && $form->isValid()){
            $commande->setNumero($commande->genererNum());
            $commande->setUtilisateur($this->getUser());
            $commande->setNomComplet($form->get('nomComplet')->getData());
            $commande->setNomComplet($form->get('adresseLivraison')->getData());
            $commande->setDateCommande(new \DateTime());
            $commande->setPrixTotal($total);
            $em->persist($commande);
        }
        dd($commande);
        
        $em->flush();

        $session->remove('panier');

        return $this->redirectToRoute('home');
    }
}
