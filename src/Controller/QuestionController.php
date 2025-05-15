<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Answer;
use App\Form\QuestionType;
use App\Form\AnswerType;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum/question')]
class QuestionController extends AbstractController
{
    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash('success', 'Votre question a été publiée avec succès!');
            return $this->redirectToRoute('app_question_show', ['id' => $question->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_question_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Question $question,
        AnswerRepository $answerRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Increment view count
        $question->incrementViews();
        $entityManager->flush();

        // Get answers for this question
        $answers = $answerRepository->findAnswersForQuestion($question);

        // Create a new answer form
        $answer = new Answer();
        $answer->setQuestion($question);
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($answer);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été publiée avec succès!');
            return $this->redirectToRoute('app_question_show', ['id' => $question->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'answer_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre question a été mise à jour avec succès!');
            return $this->redirectToRoute('app_question_show', ['id' => $question->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
            $this->addFlash('success', 'La question a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }
} 