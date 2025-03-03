<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dispoexpert', name: 'dispoexpert')]
    public function dispoexpert(): Response
    {
        return $this->render('dispo_expert/index.html.twig', [
            'controller_name' => 'DispoExpertController',
        ]);
    }
}
