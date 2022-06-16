<?php

namespace App\Controller\AdminPerso;

use App\Entity\Commentaire;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentaireController extends AbstractController
{
    /**
     * @Route("/admin/commentaire", name="admin_commentaire")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $commentaires = $doctrine->getRepository(Commentaire::class)->findBy([], ['poster_le' => 'DESC']);

        return $this->render('admin/admin_commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }
}
