<?php

namespace App\Controller;

use App\Form\ModifMdpType;
use App\Form\ModifProfilType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="mon_compte")
     */
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }

    /**
     * @Route("/compte/modifier", name="modifier_compte")
     */
    public function modifier(Request $requete, ManagerRegistry $doctrine)
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifProfilType::class, $user);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('message', 'Modification réussie');

            return $this->redirectToRoute('mon_compte');
        }

        return $this->render('compte/modifier.html.twig', [
            'form_modif' => $form->createView(),
        ]);
    }

    /**
     * @Route("/compte/modifier/mot_de_passe", name="modifier_mdp")
     */
    public function modifierMdp(Request $requete, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher)
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifMdpType::class);

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $nouvMdp = $form->get('nouveauMdp')->getData();

            if (password_verify($password, $user->getPassword())) {
                $user->setPassword($hasher->hashPassword($user, $nouvMdp));
                $doctrine->getManager()->flush();

                $this->addFlash('succes', 'Mot de passe modifier');

                return $this->redirectToRoute('mon_compte');
            }
        }

        return $this->render('compte/modifMdp.html.twig', [
            'formModifMdp' => $form->createView(),
        ]);
    }
}

// if ($requete->isMethod('POST')) {
//     $em = $doctrine->getManager();
//     $user = $this->getUser();
//     dump($requete->request->get('mdp'));
//     // On vérifie que les mots de passe sont identique
//     if ($requete->request->get('mdp') == $requete->request->get('conf-mdp')) {
//         $user->setPassword($hasher->hashPassword($user, $requete->request->get('mdp')));
//         $em->flush();
//         $this->addFlash('succes', 'Mot de passe modifier');

//         return $this->redirectToRoute('mon_compte');
//     } else {
//         $this->addFlash('erreur', 'Impossible de modifier le mot de passe');
//     }
// }

// return $this->render('compte/modifMdp.html.twig');
