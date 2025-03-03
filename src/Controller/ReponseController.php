<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Question; // Add this line to import the Question entity

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }

    #[Route('/new/{questionId}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, int $questionId): Response
{
    // Fetch the Question entity using the questionId
    $question = $entityManager->getRepository(Question::class)->find($questionId);

    if (!$question) {
        throw $this->createNotFoundException('Question not found');
    }

    // Create a new Reponse entity and associate it with the Question
    $reponse = new Reponse();
    $reponse->setDatereponse(new \DateTime());
    $reponse->setQuestion($question); // Associate the Reponse with the Question

    // Create the form
    $form = $this->createForm(ReponseType::class, $reponse);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reponse);
        $entityManager->flush();

        // Redirect to the question show page after creating the response
        return $this->redirectToRoute('app_question_show', ['id' => $questionId], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reponse/new.html.twig', [
        'reponse' => $reponse,
        'form' => $form,
        'question' => $question, // Pass the question to the template if needed
    ]);
}

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }
}