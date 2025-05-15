<?php

namespace App\Controller;

use App\Repository\RessourceEducativeRepository;
use App\Repository\CategorieRessourceRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\ConsultationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        RessourceEducativeRepository $ressourceRepo,
        CategorieRessourceRepository $categorieRepo,
        ProgrammeRepository $programmeRepo,
        ConsultationRepository $consultationRepo
    ): Response {
        // Statistiques des ressources par catégorie
        $statsCategories = $ressourceRepo->createQueryBuilder('r')
            ->select('c.nom as categorie, COUNT(r.id) as total')
            ->leftJoin('r.categorie', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        // Ressources les plus récentes
        $recentRessources = $ressourceRepo->createQueryBuilder('r')
            ->orderBy('r.datePublication', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Statistiques globales
        $stats = [
            'totalRessources' => $ressourceRepo->count([]),
            'totalCategories' => $categorieRepo->count([]),
            'totalProgrammes' => $programmeRepo->count([]),
            'totalConsultations' => $consultationRepo->count([])
        ];

        // Ressources par catégorie pour le graphique
        $categoriesData = [];
        $categoriesCount = [];
        foreach ($statsCategories as $stat) {
            $categoriesData[] = $stat['categorie'];
            $categoriesCount[] = $stat['total'];
        }

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentRessources' => $recentRessources,
            'categoriesData' => json_encode($categoriesData),
            'categoriesCount' => json_encode($categoriesCount),
            'statsCategories' => $statsCategories
        ]);
    }
} 