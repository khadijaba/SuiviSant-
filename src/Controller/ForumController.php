<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum_index', methods: ['GET'])]
    public function index(Request $request, QuestionRepository $questionRepository): Response
    {
        // Get query parameters
        $page = max(1, $request->query->getInt('page', 1));
        $category = $request->query->get('category');
        $search = $request->query->get('search');
        
        // Default to viewing the latest questions
        if ($search) {
            // Search functionality
            $questions = $questionRepository->searchQuestions($search);
            $pageCount = 1; // No pagination for search results
            $pageTitle = 'Résultats de recherche pour: ' . $search;
        } else {
            // Regular listing with pagination
            $limit = 10;
            $questionsPaginator = $questionRepository->findLatest($page, $limit, $category);
            $questions = iterator_to_array($questionsPaginator->getIterator());
            
            // Calculate page count
            $totalItems = count($questionsPaginator);
            $pageCount = ceil($totalItems / $limit);
            
            $pageTitle = $category ? 'Questions dans la catégorie: ' . $category : 'Toutes les Questions';
        }
        
        // Get popular questions for sidebar
        $popularQuestions = $questionRepository->findPopular();
        
        // Get unanswered questions for sidebar
        $unansweredQuestions = $questionRepository->findUnanswered();
        
        // Get categories for sidebar
        $categories = $questionRepository->findCategories();
        
        return $this->render('forum/index.html.twig', [
            'page_title' => $pageTitle,
            'questions' => $questions,
            'popular_questions' => $popularQuestions,
            'unanswered_questions' => $unansweredQuestions,
            'categories' => $categories,
            'current_page' => $page,
            'page_count' => $pageCount,
            'current_category' => $category,
            'search_query' => $search,
        ]);
    }

    #[Route('/questions-with-answers', name: 'app_forum_questions_with_answers', methods: ['GET'])]
    public function questionsWithAnswers(QuestionRepository $questionRepository, AnswerRepository $answerRepository): Response
    {
        // Get all questions with at least one answer, ordered by newest first
        $questions = $questionRepository->findQuestionsWithAnswers();
        
        // For each question, get its answers
        $questionsWithAnswers = [];
        foreach ($questions as $question) {
            $answers = $answerRepository->findAnswersForQuestion($question);
            $questionsWithAnswers[] = [
                'question' => $question,
                'answers' => $answers,
                'totalAnswers' => count($answers),
                'acceptedAnswer' => $this->findAcceptedAnswer($answers),
                'hasAcceptedAnswer' => $this->hasAcceptedAnswer($answers)
            ];
        }
        
        return $this->render('forum/questions_with_answers.html.twig', [
            'questionsWithAnswers' => $questionsWithAnswers,
            'categories' => $questionRepository->findCategories(),
        ]);
    }
    
    /**
     * Helper method to find the accepted answer among a collection of answers
     */
    private function findAcceptedAnswer(array $answers): ?object
    {
        foreach ($answers as $answer) {
            if ($answer->isIsAccepted()) {
                return $answer;
            }
        }
        
        return null;
    }
    
    /**
     * Helper method to check if any answer is accepted
     */
    private function hasAcceptedAnswer(array $answers): bool
    {
        foreach ($answers as $answer) {
            if ($answer->isIsAccepted()) {
                return true;
            }
        }
        
        return false;
    }
} 