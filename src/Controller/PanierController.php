<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(ProduitRepository $produitRepo, SessionInterface $session): Response
    {
        // On recupere les elements de la session
        $panier = $session->get('panier', []);

        // On recupere les données du produit dans le panier
        $datapanier = [];
        $total = 0;

        foreach ($panier as $id => $qtt) {
            $produit = $produitRepo->find($id);
            $datapanier[] = [
                'produit' => $produit,
                'qtt' => $qtt,
            ];
            $total = $produit->getPrix() * $qtt;
        }

        return $this->render('panier/index.html.twig', [
            'dataPanier' => $datapanier,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/panier/ajout/{id}", name="ajout_panier")
     */
    public function add(Produit $produit, SessionInterface $session)
    {
        // On recupere le panier actuel
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        // On verifie si le produit est deja dans le panier
        if (!empty($panier[$id])) {
            // Si oui on l'incremente.
            ++$panier[$id];
        } else {
            // Sinon on le crée avec 1 comme valeur par défaut
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/retirer/{id}", name="retirer_panier")
     */
    public function retirer(Produit $produit, SessionInterface $session)
    {
        // On recupere le panier actuel
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        // On verifie si le produit est deja dans le panier
        if (!empty($panier[$id])) {
            // Si la qtt du produit est supp a 1
            if ($panier[$id] > 1) {
                // On decremente.
                --$panier[$id];
            } else {
                // Sinon on le supprime entierement

                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set('panier', $panier);

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
