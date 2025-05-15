<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum/answer')]
class AnswerController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_answer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Answer $answer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été mise à jour avec succès!');
            return $this->redirectToRoute('app_question_show', ['id' => $answer->getQuestion()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('answer/edit.html.twig', [
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_answer_delete', methods: ['POST'])]
    public function delete(Request $request, Answer $answer, EntityManagerInterface $entityManager): Response
    {
        $questionId = $answer->getQuestion()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$answer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($answer);
            $entityManager->flush();
            $this->addFlash('success', 'La réponse a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_question_show', ['id' => $questionId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/vote/{direction}', name: 'app_answer_vote', methods: ['POST'])]
    public function vote(
        Request $request, 
        Answer $answer, 
        string $direction, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('vote'.$answer->getId(), $request->request->get('_token'))) {
            if ($direction === 'up') {
                $answer->upvote();
                $this->addFlash('success', 'Vote positif enregistré.');
            } elseif ($direction === 'down') {
                $answer->downvote();
                $this->addFlash('success', 'Vote négatif enregistré.');
            }
            
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_question_show', ['id' => $answer->getQuestion()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/accept', name: 'app_answer_accept', methods: ['POST'])]
    public function accept(Request $request, Answer $answer, EntityManagerInterface $entityManager): Response
    {
        $question = $answer->getQuestion();
        
        if ($this->isCsrfTokenValid('accept'.$answer->getId(), $request->request->get('_token'))) {
            // Reset all other answers to not accepted
            foreach ($question->getAnswers() as $existingAnswer) {
                $existingAnswer->setIsAccepted(false);
            }
            
            // Set this answer as accepted
            $answer->setIsAccepted(true);
            $entityManager->flush();
            
            $this->addFlash('success', 'Réponse marquée comme solution.');
        }

        return $this->redirectToRoute('app_question_show', ['id' => $question->getId()], Response::HTTP_SEE_OTHER);
    }
} 