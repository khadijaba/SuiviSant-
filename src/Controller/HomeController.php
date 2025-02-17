<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RessourceEducativeRepository;
use App\Repository\CategorieRessourceRepository;

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
    public function utilisateur(RessourceEducativeRepository $ressourceEducativeRepository, CategorieRessourceRepository $categorieRessourceRepository): Response
    {
        $ressources_educatives = $ressourceEducativeRepository->findAll();
        $categorie_ressources = $categorieRessourceRepository->findAll();
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'HomeController',
            'ressources_educatives' => $ressources_educatives,
            'categorie_ressources' => $categorie_ressources,
        ]);
    }
    
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(RessourceEducativeRepository $ressourceEducativeRepository,CategorieRessourceRepository $categorieRessourceRepository): Response
    {
        // Récupérer les ressources éducatives depuis le repository
        $ressourcesEducatives = $ressourceEducativeRepository->findAll();
        $categorie_ressources = $categorieRessourceRepository->findAll();
        // Passer les données au template
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'HomeController',
            'ressources_educatives' => $ressourcesEducatives,
            'categorie_ressources' => $categorie_ressources,
        ]);
    }
    
    
}
    