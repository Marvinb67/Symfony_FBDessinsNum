<?php

namespace App\Services\Panier;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    protected $session;
    protected $produitRepo;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepo)
    {
        $this->session = $session;
        $this->produitRepo = $produitRepo;
    }

    public function ajouter(int $id)
    {
        // On recupere le panier actuel
        $panier = $this->session->get('panier', []);

        // On verifie si le produit est deja dans le panier
        if (!empty($panier[$id])) {
            // Si oui on l'incremente.
            ++$panier[$id];
        } else {
            // Sinon on le crée avec 1 comme valeur par défaut
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $this->session->set('panier', $panier);
    }

    public function retirer(int $id)
    {
        // On recupere le panier actuel
        $panier = $this->session->get('panier', []);

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
        $this->session->set('panier', $panier);
    }

    public function getPanierPlein()
    {
        // On recupere les elements de la session
        $panier = $this->session->get('panier', []);

        // On recupere les données du produit dans le panier
        $datapanier = [];

        foreach ($panier as $id => $qtt) {
            $datapanier[] = [
                 'produit' => $this->produitRepo->find($id),
                 'qtt' => $qtt,
             ];
        }

        $qttPanier = 0;

        foreach ($datapanier as $produit) {
            $qttPanier += $produit['qtt'];
        }

        return $datapanier;
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->getPanierPlein() as $item) {
            $total += $item['produit']->getPrix() * $item['qtt'];
        }

        return $total;
    }
}
