<?php

namespace App\Controller;

use App\Entity\DispoExpert;
use App\Entity\Expert;
use App\Entity\Rendezvous;
use App\Form\DispoExpertType;
use App\Repository\DispoExpertRepository;
use App\Repository\ExpertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dispoexpert')]
class DispoExpertController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private DispoExpertRepository $dispoExpertRepository;
    private ExpertRepository $expertRepository;

    // Constructor for dependency injection of repositories
    public function __construct(
        EntityManagerInterface $entityManager, 
        DispoExpertRepository $dispoExpertRepository, 
        ExpertRepository $expertRepository
    ) {
        $this->entityManager = $entityManager;
        $this->dispoExpertRepository = $dispoExpertRepository;
        $this->expertRepository = $expertRepository;
    }

    #[Route('/', name: 'app_dispo_expert_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dispo_expert/index.html.twig', [
            'dispo_experts' => $this->dispoExpertRepository->findAll(),
        ]);
    }

    #[Route('/home', name: 'app_dispo_expert_index_home', methods: ['GET'])]
    public function index_home(): Response
    {
        return $this->render('base.html.twig', [
            'dispo_experts' => $this->dispoExpertRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dispo_expert_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $dispoExpert = new DispoExpert();
    
    $form = $this->createForm(DispoExpertType::class, $dispoExpert);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Ensure an expert is selected
        $selectedExpert = $dispoExpert->getExpert();
        if (!$selectedExpert) {
            $this->addFlash('error', 'Please select an expert.');
            return $this->redirectToRoute('app_dispo_expert_new');
        }

        // Persist and save
        $entityManager->persist($dispoExpert);
        $entityManager->flush();

        $this->addFlash('success', 'Dispo Expert added successfully!');
        return $this->redirectToRoute('app_dispo_expert_index');
    }

    return $this->render('dispo_expert/new.html.twig', [
        'dispo_expert' => $dispoExpert,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_dispo_expert_show', methods: ['GET'])]
    public function show(DispoExpert $dispoExpert): Response
    {
        return $this->render('dispo_expert/show.html.twig', [
            'dispo_expert' => $dispoExpert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dispo_expert_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, DispoExpert $dispoExpert, EntityManagerInterface $entityManager): Response
{
    // Ensure that the expert is set before processing
    if (!$dispoExpert->getExpert()) {
        $this->addFlash('error', 'Expert is required.');
        return $this->redirectToRoute('app_dispo_expert_index');
    }

    $form = $this->createForm(DispoExpertType::class, $dispoExpert);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Ensure an expert is selected
        $selectedExpert = $dispoExpert->getExpert();
        if (!$selectedExpert) {
            $this->addFlash('error', 'Please select an expert.');
            return $this->redirectToRoute('app_dispo_expert_edit', ['id' => $dispoExpert->getId()]);
        }

        // Persist the changes
        $entityManager->flush();

        $this->addFlash('success', 'Dispo Expert updated successfully!');
        return $this->redirectToRoute('app_dispo_expert_index');
    }

    return $this->render('dispo_expert/edit.html.twig', [
        'dispo_expert' => $dispoExpert,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}/delete', name: 'app_dispo_expert_delete', methods: ['POST'])]
    public function delete(Request $request, DispoExpert $dispoExpert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dispoExpert->getId(), $request->request->get('_token'))) {

            // ✅ Ensure relation clean up
            if ($dispoExpert->getExpert()) {
                $dispoExpert->getExpert()->removeDispoExpert($dispoExpert);
            }
            if ($dispoExpert->getRendezvous()) {
                $this->entityManager->remove($dispoExpert->getRendezvous());
            }

            $this->entityManager->remove($dispoExpert);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_dispo_expert_index');
    }
    #[Route('/api/dispo_experts', name: 'api_dispo_experts', methods: ['GET'])]
public function getDispoExperts(DispoExpertRepository $dispoExpertRepository): JsonResponse
{
    $dispos = $dispoExpertRepository->findAll();
    
    $events = [];

    foreach ($dispos as $dispo) {
        $events[] = [
            'title' => 'Expert: ' . $dispo->getExpert()->getName(),
            'start' => $dispo->getDate()->format('Y-m-d') . 'T' . $dispo->getTime()->format('H:i:s'),
            'color' => $dispo->isStatus() ? '#28a745' : '#dc3545', // Green if available, Red if unavailable
        ];
    }

    return new JsonResponse($events);
}

}
