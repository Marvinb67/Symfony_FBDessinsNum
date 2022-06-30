<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Services\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(PanierService $panierService)
    {
        if(!$this->getUser()){

            $this->addFlash('error', 'Vous devez vous connecter pour accèder à votre panier');
            return $this->redirectToRoute('acceuil');
        }
        
        return $this->render('panier/index.html.twig', [
            'dataPanier' => $panierService->getPanierPlein(),
            'total' => $panierService->getTotal(),
        ]);
    }

    /**
     * @Route("/panier/ajout/{id}", name="ajout_panier")
     */
    public function add(Produit $produit, PanierService $panierService)
    {
        $panierService->ajouter($produit->getId());

        $this->addFlash('success', 'Produit ajouté avec succès');

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/retirer/{id}", name="retirer_panier")
     */
    public function retirer(Produit $produit, PanierService $panierService)
    {
        $panierService->retirer($produit->getId());

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/supprimer/{id}", name="supprimer_panier")
     */
    public function supprimer(Produit $Produit, SessionInterface $session)
    {
        // On recupere le panier actuel
        $panier = $session->get('panier', []);
        $id = $Produit->getId();

        // On verifie si le produit est deja dans le panier
        if (!empty($panier[$id])) {
            // Si le panier n'est pas vide, on supprime l'article
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/vider", name="vider")
     */
    public function vider(SessionInterface $session)
    {
        // On recupere le panier actuel
        $session->set('panier', []);

        return $this->redirectToRoute('panier');
    }
}
