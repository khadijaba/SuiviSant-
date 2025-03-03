<?php

namespace App\Controller;

use App\Entity\SuiviProgramme;
use App\Form\SuiviProgrammeType;
use App\Repository\SuiviProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suivi/programme')]
final class SuiviProgrammeController extends AbstractController
{
    #[Route(name: 'app_suivi_programme_index', methods: ['GET'])]
    public function index(SuiviProgrammeRepository $suiviProgrammeRepository): Response
    {
        return $this->render('suivi_programme/index.html.twig', [
            'suivi_programmes' => $suiviProgrammeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_suivi_programme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $suiviProgramme = new SuiviProgramme();
        $form = $this->createForm(SuiviProgrammeType::class, $suiviProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suiviProgramme);
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi_programme/new.html.twig', [
            'suivi_programme' => $suiviProgramme,
            'form' => $form->createView(),  // Correctly pass form view
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_programme_show', methods: ['GET'])]
    public function show(SuiviProgramme $suiviProgramme): Response
    {
        return $this->render('suivi_programme/show.html.twig', [
            'suivi_programme' => $suiviProgramme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_programme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuiviProgramme $suiviProgramme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuiviProgrammeType::class, $suiviProgramme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('suivi_programme/edit.html.twig', [
            'suivi_programme' => $suiviProgramme,
            'form' => $form->createView(),  // Correctly pass form view
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_programme_delete', methods: ['POST'])]
    public function delete(Request $request, SuiviProgramme $suiviProgramme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suiviProgramme->getId(), $request->request->get('_token'))) {  // Correct access to CSRF token
            $entityManager->remove($suiviProgramme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suivi_programme_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/statistique/update', name: 'update_statistique')]
public function updateStatistique(EntityManagerInterface $entityManager, UserRepository $userRepository, SuiviProgrammeRepository $suiviRepository): Response
{
    // Récupérer les statistiques
    $totalUsers = $userRepository->count([]);
    $newParticipants = $userRepository->countNewParticipants(); // Crée cette méthode dans UserRepository
    $followUps = $suiviRepository->count([]);
    $issues = 5; // À récupérer dynamiquement selon tes critères

    // Créer une nouvelle instance de Statistique
    $statistique = new Statistique();
    $statistique->setTotalUsers($totalUsers);
    $statistique->setNewParticipants($newParticipants);
    $statistique->setFollowUps($followUps);
    $statistique->setIssues($issues);
    $statistique->setDate(new \DateTime());

    // Enregistrer en base de données
    $entityManager->persist($statistique);
    $entityManager->flush();

    return $this->redirectToRoute('dashboard'); // Redirige vers ton dashboard
}

}
