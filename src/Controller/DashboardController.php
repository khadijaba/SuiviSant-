<?php

namespace App\Controller;

use App\Repository\UtilisateurrRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UtilisateurrRepository $utilisateurrRepository): Response
    {
        // Récupérer tous les utilisateurs depuis la base de données
        $utilisateurrs = $utilisateurrRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'utilisateurrs' => $utilisateurrs,
        ]);
    }
}


