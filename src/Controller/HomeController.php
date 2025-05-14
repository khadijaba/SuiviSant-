<?php

namespace App\Controller;

use App\Repository\ExpertRepository;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(ExpertRepository $expertRepository): Response
    {
        // Fetch programmes directly using PDO
        $programmes = [];
        try {
            $pdo = new \PDO('mysql:host=localhost;dbname=pideva', 'root', '');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->query("SELECT * FROM programme");
            $programmes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error but don't show to users
            error_log('Database error: ' . $e->getMessage());
        }
        
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
            'experts' => $expertRepository->findAll(),
            'programmes' => $programmes,
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
    public function dashboard(ExpertRepository $expertRepository, RendezVousRepository $rendezVousRepository): Response
    {
        $totalAppointments = $rendezVousRepository->count([]);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
            'experts' => $expertRepository->findAll(),
            'totalAppointments' => $totalAppointments, // Pass total appointments to dashboard
        ]);
    }

    #[Route('/dispoexpert', name: 'dispoexpert')]
    public function dispoexpert(): Response
    {
        return $this->render('dispo_expert/index.html.twig', [
            'controller_name' => 'DispoExpertController',
        ]);
    }

    #[Route('/admin/programme', name: 'admin_programme')]
    public function adminProgramme(): Response
    {
        // Fetch programmes directly using PDO
        $programmes = [];
        try {
            $pdo = new \PDO('mysql:host=localhost;dbname=pideva', 'root', '');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->query("SELECT * FROM programme");
            $programmes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error but don't show to users
            error_log('Database error: ' . $e->getMessage());
        }
        
        return $this->render('admin/programme.html.twig', [
            'programmes' => $programmes,
        ]);
    }
    
    #[Route('/programme/participer/{nom}', name: 'app_programme_participer')]
    public function participer($nom): Response
    {
        // Here you would add logic to register the user for the programme
        // For now, just redirect back to homepage with a success message
        
        $this->addFlash('success', "Vous avez été inscrit au programme '" . $nom . "' avec succès!");
        return $this->redirectToRoute('app_home');
    }
}
