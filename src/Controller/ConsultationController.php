<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Rapport;
use App\Form\RapportType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/consultation')]
class ConsultationController extends AbstractController
{
    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        // Ne montrer que les consultations de l'utilisateur connecté
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $consultation->setUtilisateur($this->getUser());

        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();
            $this->addFlash('success', 'La consultation a été créée avec succès.');
            return $this->redirectToRoute('app_consultation_index');
        }

        return $this->render('consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        try {
            $this->denyAccessUnlessGranted('VIEW', $consultation);
            
            return $this->render('consultation/show.html.twig', [
                'consultation' => $consultation,
            ]);
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour voir cette consultation.');
            return $this->redirectToRoute('app_consultation_index');
        }
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted('EDIT', $consultation);
            
            $form = $this->createForm(ConsultationType::class, $consultation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'La consultation a été modifiée avec succès.');
                return $this->redirectToRoute('app_consultation_index');
            }

            return $this->render('consultation/edit.html.twig', [
                'consultation' => $consultation,
                'form' => $form->createView(),
            ]);
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour modifier cette consultation.');
            return $this->redirectToRoute('app_consultation_index');
        }
    }

    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]
    public function delete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted('DELETE', $consultation);
            
            if ($this->isCsrfTokenValid('delete' . $consultation->getId(), $request->request->get('_token'))) {
                $entityManager->remove($consultation);
                $entityManager->flush();
                $this->addFlash('success', 'La consultation a été supprimée avec succès.');
            }

            return $this->redirectToRoute('app_consultation_index');
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour supprimer cette consultation.');
            return $this->redirectToRoute('app_consultation_index');
        }
    }

    #[Route('/{id}/rapport/new', name: 'app_consultation_rapport_new', methods: ['GET', 'POST'])]
    public function newRapport(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted('EDIT', $consultation);
            
            $rapport = new Rapport();
            $rapport->setConsultation($consultation);
            
            $form = $this->createForm(RapportType::class, $rapport);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($rapport);
                $entityManager->flush();
                $this->addFlash('success', 'Le rapport a été ajouté avec succès.');
                return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()]);
            }

            return $this->render('rapport/new.html.twig', [
                'consultation' => $consultation,
                'form' => $form->createView(),
            ]);
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour ajouter un rapport à cette consultation.');
            return $this->redirectToRoute('app_consultation_index');
        }
    }
}
