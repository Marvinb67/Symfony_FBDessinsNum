<?php

namespace App\Controller\AdminPerso;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminActuController extends AbstractController
{
    /**
     * @Route("/admin/actu", name="admin_actu")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $actualites = $doctrine->getRepository(Actualite::class)->findBy([], ['poster_le' => 'DESC']);

        return $this->render('admin/admin_actu/index.html.twig', [
            'actualites' => $actualites,
        ]);
    }

    /**
     * @Route("/admin/actu/ajout", name="ajout_actu")
     * @Route("/admin/actu/modif/{id}", name="modif_actu")
     */
    public function add(ManagerRegistry $doctrine, Actualite $actualite = null, Request $requete, SluggerInterface $slugger): Response
    {
        if (!$actualite) {
            $actualite = new Actualite();
        }

        $em = $doctrine->getManager();

        $form = $this->createForm(ActualiteType::class, $actualite);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualite = $form->getData();
            $actualite->setUtilisateur($this->getUser());

            $em->persist($actualite);
            $em->flush();

            return $this->redirectToRoute('admin_actu');
        }

        return $this->render('admin/admin_actu/ajoutModif.html.twig', [
            'formActu' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/actu/supprimer/{id}", name="supprimer_actu")
     */
    public function supprimer(ManagerRegistry $doctrine, Actualite $actualite): Response
    {
        $em = $doctrine->getManager();

        $em->remove($actualite);

        $em->flush();

        return $this->redirectToRoute('admin_actu');
    }
}
