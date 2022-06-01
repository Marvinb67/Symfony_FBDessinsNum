<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="index_boutique")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine->getRepository(Produit::class)->findAll();

        return $this->render('boutique/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    // /**
    //  * @Route("/boutique/add", name="add_produit")
    //  */
    // public function add(ManagerRegistry $doctrine, Produit $produit = null, Request $requete, SluggerInterface $slugger): Response
    // {
    //     $em = $doctrine->getManager();

    //     $form = $this->createForm(ProduitType::class, $produit);

    //     $form->handleRequest($requete);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $produit = $form->getData();

    //         // upload d'image
    //         $imageUpload = $form->get('image')->getData();

    //         // Cette condition est nécessaire car le champ image étant optionnel, le fichier ne doit ètre uploader qu'a l'ajout d'un produit
    //         if ($imageUpload) {
    //             $nomOriginal = pathinfo($imageUpload->getClientOriginalName(), PATHINFO_FILENAME);
    //             // Permet d'inclure le nom du fichier dans l'URL
    //             $nomSecur = $slugger->slug($nomOriginal);
    //             $nouveauNom = $nomSecur.'-'.uniqid().'.'.$imageUpload->guessExtension();

    //             try {
    //                 $imageUpload->move($this->getParameter('image_upload'), $nouveauNom);
    //             } catch (FileException $e) {
    //                 throw $e->getMessage($nouveauNom);
    //             }

    //             $produit->setImage($nouveauNom);
    //         }
    //         $em->persist($produit);
    //         $em->flush();

    //         return $this->redirectToRoute('index_boutique');
    //     }

    //     return $this->render('boutique/add.html.twig', [
    //         'formProduit' => $form->createView(),
    //     ]);
    // }
}
