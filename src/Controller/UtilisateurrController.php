<?php

namespace App\Controller;

use App\Entity\Utilisateurr;
use App\Form\UtilisateurrType;
use App\Repository\UtilisateurrRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/utilisateurr')]
final class UtilisateurrController extends AbstractController
{
    #[Route(name: 'app_utilisateurr_index', methods: ['GET'])]
    public function index(UtilisateurrRepository $utilisateurrRepository): Response
    {
        return $this->render('utilisateurr/index.html.twig', [
            'utilisateurrs' => $utilisateurrRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_utilisateurr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateurr = new Utilisateurr();
        $form = $this->createForm(UtilisateurrType::class, $utilisateurr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateurr);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateurr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateurr/new.html.twig', [
            'utilisateurr' => $utilisateurr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurr_show', methods: ['GET'])]
    public function show(Utilisateurr $utilisateurr): Response
    {
        return $this->render('utilisateurr/show.html.twig', [
            'utilisateurr' => $utilisateurr,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateurr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateurr $utilisateurr, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateurrType::class, $utilisateurr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateurr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateurr/edit.html.twig', [
            'utilisateurr' => $utilisateurr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateurr_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateurr $utilisateurr, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateurr->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($utilisateurr);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateurr_index', [], Response::HTTP_SEE_OTHER);
    }
}
