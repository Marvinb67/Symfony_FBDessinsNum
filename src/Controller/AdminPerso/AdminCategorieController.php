<?php

namespace App\Controller\AdminPerso;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategorieController extends AbstractController
{
    /**
     * @Route("/admin/categorie", name="admin_categorie")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(Categorie::class)->findBy([], ['id' => 'ASC']);

        return $this->render('admin/admin_categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("admin/categorie/ajout", name="admin_categorie_ajout")
     * @Route("admin/categorie/modifier/{id}", name="admin_categorie_modifier")
     */
    public function ajoutModif(ManagerRegistry $doctrine, Categorie $categorie = null, Request $requete)
    {
        if (!$categorie) {
            $categorie = new Categorie();
        }

        $em = $doctrine->getManager();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categorie');
        }

        return $this->render('admin/admin_categorie/ajoutModif.html.twig', [
            'formCategorie' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categorie/supprimer/{id}", name="admin_categorie_supprimer")
     */
    public function supprimer(ManagerRegistry $doctrine, Categorie $categorie)
    {
        $em = $doctrine->getManager();

        $em->remove($categorie);

        $em->flush();

        return $this->redirectToRoute('admin_categorie');
    }
}
