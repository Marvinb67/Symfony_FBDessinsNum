<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\ConfirmCommandeType;
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
    public function index(ManagerRegistry $doctrine, Request $requete): Response
    {
        $panier = $this->panierServices->getPanierPlein();
        $total = $this->panierServices->getTotal();

        if (!$this->getUser()->getAdresses()->getValues()) {
            $this->addFlash('erreur', 'Veuillez renseigner une adresse pour passer à la commande');

            return $this->redirectToRoute('ajouter_adresse');
        }

        $form = $this->createForm(ConfirmCommandeType::class, null, ['user' => $this->getUser()]);
        $form->handleRequest($requete);

        return $this->render('commande/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
            'formConfirm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/commande/validation", name="validation_commande")
     */
    public function ajouter(Request $requete, ManagerRegistry $doctrine, PanierService $panierService)
    {
        $em = $doctrine->getManager();
        $session = $requete->getSession();

        $commande = new Commande();
        if (!$session->has('panier')) {
            return $this->redirectToRoute('index_boutique');
        }

        $form = $this->createForm(ConfirmCommandeType::class, null, ['user' => $this->getUser()]);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse = $form->getData();

            $session->set('adresse', $adresse);
        }

        $panier = $session->get('panier');
        // $produits = $em->getRepository(Produit::class)->findBy(array_keys($panier));
        $produits = [];
        foreach (array_keys($session->get('panier')) as $prod) {
            $produits[] = $em->getRepository(Produit::class)->find($prod);
        }
        dump($produits);
        $panierCmd = [];
        $total = $panierService->getTotal();
        foreach ($produits as $article) {
            $panierCmd[$article->getId()] = [
                'nom' => $article->getNom(),
                'prix' => $article->getPrix(),
                'quantite' => $panier[$article->getId()],
            ];
            $cmdArticle = new Panier();
            $cmdArticle->setQuantite($panier[$article->getId()]);
            $cmdArticle->setProduit($article);
            $cmdArticle->setCommande($commande);
            $em->persist($cmdArticle);
        }

        $commande->setNumero($commande->genererNum());
        $commande->setUtilisateur($this->getUser());
        $commande->setNomComplet($this->getUser());
        $commande->setDateCommande(new \DateTime());
        // $commande->setAdresseLivraison($adresse['adresse']);
        $commande->setPrixTotal($total);
        $em->persist($commande);

        dd($commande);
        dd($panierCmd);
        dd($cmdArticle);
    }
}
