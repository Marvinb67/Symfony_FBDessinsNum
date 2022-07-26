<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{
    /**
     * @Route("/adresse", name="adresse")
     */
    public function index(AdresseRepository $adresses): Response
    {
        return $this->render('adresse/index.html.twig', [
            'adresses' => $adresses->findAll(),
        ]);
    }

    /**
     * @Route("/adresse/ajouter", name="ajouter_adresse")
     * @Route("/adresse/modiifier/{id}", name="modifier_adresse")
     */
    public function ajouter(ManagerRegistry $doctrine, Adresse $adresse = null, Request $requete): Response
    {
        if (!$adresse) {
            $adresse = new Adresse();
        }

        $em = $doctrine->getManager();
        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse = $form->getData();
            $adresse->setUtilisateur($this->getUser());

            $em->persist($adresse);
            $em->flush();

            return $this->redirectToRoute('validation_commande');
        }

        return $this->render('adresse/ajoutModif.html.twig', [
            'formAdresse' => $form->createView(),
        ]);
    }

    /**
     * @Route("/adresse/supprimer/{id}", name="supprimer_adresse")
     */
    public function supprimer(ManagerRegistry $doctrine, Adresse $adresse): Response
    {
        $em = $doctrine->getManager();

        $em->remove($adresse);

        $em->flush();

        return $this->redirectToRoute('checkout');
    }
}
