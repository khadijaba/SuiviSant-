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
    #[Route(name: 'app_rapport_index', methods: ['GET'])]
    public function index(RapportRepository $rapportRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'rapports' => $rapportRepository->findAll(),
        ]);
    }

    #[Route('/new/{consultation_id}', name: 'app_rapport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $consultation_id): Response
    {
        // Trouver la consultation par son ID
        $consultation = $entityManager->getRepository(Consultation::class)->find($consultation_id);
    
        // Si la consultation n'existe pas, afficher une erreur
        if (!$consultation) {
            throw $this->createNotFoundException('Consultation not found');
        }
    
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
    
            // Rediriger vers la page de consultation une fois le rapport ajouté
            return $this->redirectToRoute('app_consultation_show', ['id' => $consultation->getId()]);
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

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la suppression est sécurisée avec un token CSRF
        if ($this->isCsrfTokenValid('delete' . $rapport->getId(), $request->get('_token'))) {
            $consultation = $rapport->getConsultation();

            if ($consultation) {
                // Dissocier le rapport de la consultation
                $rapport->setConsultation(null); // Dissociation explicite
                $entityManager->flush(); // Sauvegarder l'état modifié de la consultation
            }

            // Supprimer le rapport
            $entityManager->remove($rapport);
            $entityManager->flush(); // Persister la suppression du rapport
        }

        // Rediriger vers la liste des rapports après la suppression
        return $this->redirectToRoute('app_rapport_index');
    }

   


}
