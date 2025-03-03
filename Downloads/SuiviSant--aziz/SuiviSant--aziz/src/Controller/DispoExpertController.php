<?php

namespace App\Controller;

use App\Entity\DispoExpert;
use App\Form\DispoExpertType;
use App\Repository\DispoExpertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dispoexpert')]
final class DispoExpertController extends AbstractController
{
    #[Route(name: 'app_dispo_expert_index', methods: ['GET'])]
    public function index(DispoExpertRepository $dispoExpertRepository): Response
    {
        return $this->render('dispo_expert/index.html.twig', [
            'dispo_experts' => $dispoExpertRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dispo_expert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dispoExpert = new DispoExpert();
        $form = $this->createForm(DispoExpertType::class, $dispoExpert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dispoExpert);
            $entityManager->flush();

            return $this->redirectToRoute('app_dispo_expert_index', [], Response::HTTP_SEE_OTHER);
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
        $form = $this->createForm(DispoExpertType::class, $dispoExpert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dispo_expert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dispo_expert/edit.html.twig', [
            'dispo_expert' => $dispoExpert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dispo_expert_delete', methods: ['POST'])]
    public function delete(Request $request, DispoExpert $dispoExpert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dispoExpert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dispoExpert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dispo_expert_index', [], Response::HTTP_SEE_OTHER);
    }
}
