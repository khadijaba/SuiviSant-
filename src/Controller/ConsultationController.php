<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Rapport;
use App\Form\ConsultationType;
use App\Form\RapportType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/consultation')]
final class ConsultationController extends AbstractController
{
    #[Route(name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            $this->addFlash('success', 'Consultation ajoutée avec succès.');
            return $this->redirectToRoute('app_consultation_index');
        }

        return $this->render('consultation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $consultation,
            'rapport' => $consultation->getRapport(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ConsultationType::class, $consultation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Dissocier le rapport de la consultation avant de l'associer à la nouvelle
        if ($consultation->getRapport() !== null) {
            $consultation->getRapport()->setConsultation(null);
        }

        // Associer le rapport à la consultation si le rapport est sélectionné
        $rapport = $consultation->getRapport();
        if ($rapport !== null) {
            $rapport->setConsultation($consultation);
        }

        // Sauvegarder les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Consultation mise à jour.');
        return $this->redirectToRoute('app_consultation_index');
    }

    return $this->render('consultation/edit.html.twig', [
        'form' => $form->createView(),
        'consultation' => $consultation,
    ]);
}


    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]public function delete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $consultation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($consultation);
            $entityManager->flush();
            $this->addFlash('success', 'Consultation et rapport supprimés avec succès.');
        }
    
        return $this->redirectToRoute('app_consultation_index');
    }
    

    #[Route('/{id}/add-rapport', name: 'app_consultation_add_rapport', methods: ['GET', 'POST'])]
    public function addRapport(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        if ($consultation->getRapport() !== null) {
            $this->addFlash('error', 'Un rapport est déjà attaché à cette consultation.');
            return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()]);
        }

        $rapport = new Rapport();
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $consultation->setRapport($rapport);
            $entityManager->persist($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'Rapport ajouté avec succès.');
            return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()]);
        }

        return $this->render('consultation/add_rapport.html.twig', [
            'form' => $form->createView(),
            'consultation' => $consultation,
        ]);
    }
    #[Route('/admin/consultations', name: 'admin_consultation_index')]
    public function adminIndex(ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findAll();
    
        return $this->render('admin/consultations.html.twig', [
            'consultations' => $consultations,
        ]);
    }
    


}
