<?php

namespace App\Controller\FrontEnd;

use App\Entity\Consultation;
use App\Repository\ConsultationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/consultation')]
class ConsultationController extends AbstractController
{
    #[Route('/', name: 'front_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        $user = $this->getUser();
        return $this->render('front_end/consultation/index.html.twig', [
            'consultations' => $consultationRepository->findByUser($user),
        ]);
    }

    #[Route('/{id}', name: 'front_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        // Vérifier que l'utilisateur actuel est bien le propriétaire de la consultation
        if ($consultation->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette consultation.');
        }

        return $this->render('front_end/consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }
} 