<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RessourceEducativeRepository;
use App\Repository\CategorieRessourceRepository;
use App\Repository\ConsultationRepository;
use App\Repository\ExpertRepository;
use App\Repository\RendezVousRepository;
use App\Repository\DispoExpertRepository;
use App\Repository\ProgrammeRepository;
use App\Entity\Expert;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(
        RessourceEducativeRepository $ressourceEducativeRepository, 
        CategorieRessourceRepository $categorieRessourceRepository,
        ConsultationRepository $consultationRepository,
        ExpertRepository $expertRepository,
        RendezVousRepository $rendezVousRepository,
        ProgrammeRepository $programmeRepository
    ): Response {
        // Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        
        // Récupérer les consultations de l'utilisateur
        $consultations = $consultationRepository->findByUser($user);
        
        $ressources_educatives = $ressourceEducativeRepository->findAll();
        $categorie_ressources = $categorieRessourceRepository->findAll();
        
        // Récupérer tous les experts
        $experts = $expertRepository->findAll();

        // Récupérer les rendez-vous
        $rendezVous = $rendezVousRepository->findAll();

        // Récupérer tous les programmes
        $programmes = $programmeRepository->findAll();

        // Récupération des statistiques
        $query = $ressourceEducativeRepository->createQueryBuilder('r')
            ->select('c.nom AS categoryName, COUNT(r.id) AS totalResources')
            ->join('r.categorie', 'c')
            ->groupBy('c.nom')
            ->getQuery();

        $results = $query->getResult();

        // Transformation des données pour Chart.js
        $categories = [];
        $counts = [];
        foreach ($results as $result) {
            $categories[] = $result['categoryName'];
            $counts[] = $result['totalResources'];
        }

        return $this->render('utilisateur/index.html.twig', [
            'ressources_educatives' => $ressources_educatives,
            'categorie_ressources' => $categorie_ressources,
            'categories' => json_encode($categories),
            'counts' => json_encode($counts),
            'consultations' => $consultations,
            'experts' => $experts,
            'rendez_vous' => $rendezVous,
            'programmes' => $programmes,
        ]);
    }

    #[Route('/expert/{id}/calendar', name: 'expert_calendar')]
    public function expertCalendar(
        Expert $expert,
        DispoExpertRepository $dispoExpertRepository
    ): Response {
        // Récupérer les disponibilités de l'expert
        $disponibilites = $dispoExpertRepository->findBy(['expert' => $expert]);

        return $this->render('rendez_vous/calendar.html.twig', [
            'expert' => $expert,
            'disponibilites' => $disponibilites
        ]);
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(
        RessourceEducativeRepository $ressourceEducativeRepository,
        CategorieRessourceRepository $categorieRessourceRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {
        $sortBy = $_GET['sort'] ?? 'titre'; // Trier par titre par défaut

        // Générer la requête triée
        $queryBuilder = $ressourceEducativeRepository->createQueryBuilder('r')
            ->leftJoin('r.categorie', 'c')
            ->addSelect('c');

        if ($sortBy === 'date') {
            $queryBuilder->orderBy('r.datePublication', 'DESC');
        } elseif ($sortBy === 'categorie') {
            $queryBuilder->orderBy('c.nom', 'ASC');
        } else { // Trier par titre par défaut
            $queryBuilder->orderBy('r.titre', 'ASC');
        }

        $ressourcesEducatives = $queryBuilder->getQuery()->getResult();
        $categorie_ressources = $categorieRessourceRepository->findAll();
        $rendezVous = $rendezVousRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'ressources_educatives' => $ressourcesEducatives,
            'categorie_ressources' => $categorie_ressources,
            'sortBy' => $sortBy, // Envoyer le critère de tri à la vue
            'rendez_vous' => $rendezVous,
        ]);
    }
}
    