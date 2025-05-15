<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RessourceEducativeRepository;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function statistics(RessourceEducativeRepository $ressourceEducativeRepository): Response
    {
        // Remplacer $repository par $ressourceEducativeRepository
        try {
            $query = $ressourceEducativeRepository->createQueryBuilder('r')
                ->select('c.nom AS categoryName, COUNT(r.id) AS totalResources')  // Utilise 'c.nom' pour accéder au champ de la catégorie
                ->join('r.categorie', 'c')  // Jointure sur l'entité 'Categorie'
                ->groupBy('c.nom')  // Grouper par le nom de la catégorie
                ->getQuery();

            // Exécuter la requête et récupérer les résultats
            $results = $query->getResult();
        } catch (\Exception $e) {
            // Gérer les erreurs (par exemple, si la connexion à la base de données échoue)
            return $this->render('error.html.twig', [
                'message' => 'Une erreur est survenue lors de l\'exécution de la requête.',
            ]);
        }

        // Passer les résultats à la vue (template)
        return $this->render('statistics/index.html.twig', [
            'results' => $results,
        ]);
    }
}
