<?php

namespace App\Controller;

use App\Entity\ProgrammeSante;
use App\Form\ProgrammeSanteType;
use App\Repository\ProgrammeSanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/programme/sante')]
final class ProgrammeSanteController extends AbstractController
{
    #[Route(name: 'app_programme_sante_index', methods: ['GET'])]
    public function index(ProgrammeSanteRepository $programmeSanteRepository): Response
    {
        return $this->render('programme_sante/index.html.twig', [
            'programme_santes' => $programmeSanteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_programme_sante_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programmeSante = new ProgrammeSante();
        $form = $this->createForm(ProgrammeSanteType::class, $programmeSante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($programmeSante);
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_sante_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('programme_sante/new.html.twig', [
            'programme_sante' => $programmeSante,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_sante_show', methods: ['GET'])]
    public function show(ProgrammeSante $programmeSante): Response
    {
        return $this->render('programme_sante/show.html.twig', [
            'programme_sante' => $programmeSante,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_programme_sante_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProgrammeSante $programmeSante, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgrammeSanteType::class, $programmeSante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_sante_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('programme_sante/edit.html.twig', [
            'programme_sante' => $programmeSante,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_sante_delete', methods: ['POST'])]
    public function delete(Request $request, ProgrammeSante $programmeSante, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programmeSante->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($programmeSante);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_programme_sante_index', [], Response::HTTP_SEE_OTHER);
    }
}
