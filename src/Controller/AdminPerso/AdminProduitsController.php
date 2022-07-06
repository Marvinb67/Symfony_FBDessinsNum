<?php

namespace App\Controller\AdminPerso;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminProduitsController extends AbstractController
{
    /**
     * @Route("/admin/produits", name="admin_produits")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine->getRepository(Produit::class)->findBy([], ['nom' => 'ASC']);

        return $this->render('admin/admin_produits/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * @Route("/admin/produits/ajout", name="admin_produits_ajout")
     * @Route("/admin/produits/modifier/{id}", name="admin_produits_modifier")
     */
    public function add(ManagerRegistry $doctrine, Produit $produit = null, Request $requete, SluggerInterface $slugger): Response
    {
        $em = $doctrine->getManager();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $form->getData();

            // upload d'image
            $imageUpload = $form->get('image')->getData();

            // Cette condition est nécessaire car le champ image étant optionnel, le fichier ne doit ètre uploader qu'a l'ajout d'un produit
            if ($imageUpload) {
                $nomOriginal = pathinfo($imageUpload->getClientOriginalName(), PATHINFO_FILENAME);
                // Permet d'inclure le nom du fichier dans l'URL
                $nomSecur = $slugger->slug($nomOriginal);
                $nouveauNom = $nomSecur.'-'.uniqid().'.'.$imageUpload->guessExtension();

                try {
                    $imageUpload->move($this->getParameter('image_upload'), $nouveauNom);
                } catch (FileException $e) {
                    throw $e->getMessage($nouveauNom);
                }

                $produit->setImage($nouveauNom);
            }
            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('admin_produits');
        }

        return $this->render('admin/admin_produits/ajoutModif.html.twig', [
            'formProduit' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/produits/supprimer/{id}", name="admin_produits_supprimer")
     */
    public function supprimer(ManagerRegistry $doctrine, Produit $produit): Response
    {
        $em = $doctrine->getManager();

        $image = $produit->getImage();

        if ($image) {
            $nomImage = $this->getParameter("image_upload") . '/' . $image;

            if(file_exists($nomImage)){
                unlink($nomImage);
            }
        }

        $em->remove($produit);

        $em->flush();

        return $this->redirectToRoute('admin_produits');
    }
}
