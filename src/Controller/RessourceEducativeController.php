<?php

namespace App\Controller;

use App\Entity\RessourceEducative;
use App\Form\RessourceEducativeType;
use App\Repository\RessourceEducativeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ressource/educative', name: 'app_ressource_educative_')] // Préfixe pour toutes les routes liées à RessourceEducative
final class RessourceEducativeController extends AbstractController
{
    /**
     * Affiche la liste des ressources éducatives.
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(RessourceEducativeRepository $ressourceEducativeRepository): Response
    {
        return $this->render('ressource_educative/index.html.twig', [
            'ressource_educatives' => $ressourceEducativeRepository->findAll(),
        ]);
    }

    /**
     * Ajoute une nouvelle ressource éducative.
     */
 #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $ressourceEducative = new RessourceEducative();
    $form = $this->createForm(RessourceEducativeType::class, $ressourceEducative);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $entityManager->persist($ressourceEducative);
            $entityManager->flush();

            $this->addFlash('success', 'Ressource éducative ajoutée avec succès !');

            return $this->redirectToRoute('app_ressource_educative_show', [
                'id' => $ressourceEducative->getId(),
            ], Response::HTTP_SEE_OTHER);
        } else {
            dump($form->getErrors(true)); // 🔍 Affiche les erreurs de validation dans la barre de debug Symfony
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }
    }

    return $this->render('ressource_educative/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    /**
     * Affiche une ressource éducative spécifique.
     */
    #[Route('/show/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(RessourceEducativeRepository $ressourceEducativeRepository, int $id): Response
    {
        $ressourceEducative = $ressourceEducativeRepository->find($id);

        if (!$ressourceEducative) {
            throw $this->createNotFoundException("Ressource non trouvée !");
        }

        return $this->render('ressource_educative/show.html.twig', [
            'ressource_educative' => $ressourceEducative,
        ]);
    }

    /**
     * Modifie une ressource éducative existante.
     */
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
public function edit(Request $request, RessourceEducative $ressourceEducative, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(RessourceEducativeType::class, $ressourceEducative);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Ressource éducative mise à jour avec succès !');
        return $this->redirectToRoute('app_ressource_educative_show', [
            'id' => $ressourceEducative->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
    return $this->render('ressource_educative/edit.html.twig', [
        'ressource_educative' => $ressourceEducative,
        'form' => $form->createView(),
    ]);
    
    
}
    /**
     * Supprime une ressource éducative.
     */
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, RessourceEducativeRepository $ressourceEducativeRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $ressourceEducative = $ressourceEducativeRepository->find($id);

        if (!$ressourceEducative) {
            throw $this->createNotFoundException("Ressource non trouvée !");
        }

        if ($this->isCsrfTokenValid('delete' . $ressourceEducative->getId(), $request->request->get('_token'))) {
            // Suppression de la ressource
            $entityManager->remove($ressourceEducative);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ressource_educative_index', [], Response::HTTP_SEE_OTHER);
    }
}