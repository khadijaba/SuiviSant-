<?php

namespace App\Controller;
use App\Entity\ProgrammeSante;
use App\Form\ProgrammeSanteType;
use App\Repository\ProgrammeSanteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(ProgrammeSanteRepository $programmeSanteRepository): Response
    {
        return $this->render('programme_user/index.html.twig', [
            'programme_santes' => $programmeSanteRepository->findAll(),
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


}
