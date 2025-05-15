<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\DispoExpert;
use App\Service\EmailService;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use App\Repository\DispoExpertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rendezvous')]
final class RendezVousController extends AbstractController
{
    #[Route(name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'rendez_vouses' => $rendezVousRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rendez_vous_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    DispoExpertRepository $dispoExpertRepository,
    EmailService $emailService
): Response {
    $rendezVous = new RendezVous();

    $dispoId = $request->query->get('dispoId');
    $selectedDispo = null;

    if ($dispoId) {
        $selectedDispo = $dispoExpertRepository->find($dispoId);
        if ($selectedDispo) {
            $rendezVous->setDispoExpert($selectedDispo);
        }
    }

    $form = $this->createForm(RendezVousType::class, $rendezVous, [
        'selected_dispo' => $selectedDispo,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($selectedDispo) {
            $selectedDispo->setStatus(false);
            $entityManager->persist($selectedDispo);
        }

        $entityManager->persist($rendezVous);
        $entityManager->flush();

        // Send confirmation email
        $emailService->sendRendezvousConfirmation(
            $rendezVous->getEmail(),
            $rendezVous->getPhoneNumber(),
            $selectedDispo ? $selectedDispo->getDate()->format('Y-m-d') : 'Not specified',
            $selectedDispo ? $selectedDispo->getTime()->format('H:i') : 'Not specified',
            $selectedDispo ? $selectedDispo->getLocation() : 'Not specified'
        );

        $this->addFlash('success', 'Your appointment has been booked successfully! A confirmation email has been sent.');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('rendez_vous/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVous $rendezVous): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dispoExpert = $rendezVous->getDispoExpert();
            if ($dispoExpert) {
                $dispoExpert->setStatus(false); // Ensure slot remains unavailable
                $entityManager->persist($dispoExpert);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Appointment updated successfully!');
            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vous' => $rendezVous,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $rendezVous->getId(), $request->request->get('_token'))) {
            $dispoExpert = $rendezVous->getDispoExpert();
            
            if ($dispoExpert) {
                // ✅ Restore availability before deleting the appointment
                $dispoExpert->setStatus(true);
                $rendezVous->setDispoExpert(null); // Detach the rendezvous from dispoExpert
                $entityManager->persist($dispoExpert);
            }

            $entityManager->remove($rendezVous);
            $entityManager->flush();

            $this->addFlash('success', 'Appointment deleted successfully. The slot is now available again.');
        }

        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }
}
