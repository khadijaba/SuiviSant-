<?php

namespace App\Controller;
use Knp\Snappy\Pdf;
use App\Entity\Rapport;
use App\Entity\Consultation;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapport')]
final class RapportController extends AbstractController
{
    #[Route('/consultation/{consultation}', name: 'app_rapport_index', methods: ['GET'])]
    public function index(Consultation $consultation, RapportRepository $rapportRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'consultation' => $consultation,
            'rapports' => $rapportRepository->findBy(['consultation' => $consultation]),
        ]);
    }

    #[Route('/new/consultation/{consultation}', name: 'app_rapport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Consultation $consultation): Response
    {
        // Créer un nouveau rapport
        $rapport = new Rapport();
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Lier le rapport à la consultation
            $rapport->setConsultation($consultation);
    
            // Persister le rapport
            $entityManager->persist($rapport);
            $entityManager->flush();
    
            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le rapport a été créé avec succès.');
            
            // Rediriger vers la liste des consultations
            return $this->redirectToRoute('app_consultation_index');
        }
    
        return $this->render('rapport/new.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_show', methods: ['GET'])]
    public function show(Rapport $rapport): Response
    {
        return $this->render('rapport/show.html.twig', [
            'rapport' => $rapport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_index', ['consultation' => $rapport->getConsultation()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rapport->getId(), $request->request->get('_token'))) {
            $consultation = $rapport->getConsultation();
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_consultation_index');
    }
}
