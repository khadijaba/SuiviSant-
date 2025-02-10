<?php

namespace App\Controller;

use App\Entity\CategorieRessource;
use App\Form\CategorieRessourceType;
use App\Repository\CategorieRessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie/ressource')]
final class CategorieRessourceController extends AbstractController
{
    #[Route('/', name: 'app_categorie_ressource_index', methods: ['GET'])]
    public function index(CategorieRessourceRepository $categorieRessourceRepository): Response
    {
        return $this->render('categorie_ressource/index.html.twig', [
            'categorie_ressources' => $categorieRessourceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_ressource_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieRessource = new CategorieRessource();
        $form = $this->createForm(CategorieRessourceType::class, $categorieRessource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieRessource);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_ressource_index');
        }

        return $this->render('categorie_ressource/new.html.twig', [
            'categorie_ressource' => $categorieRessource,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_ressource_show', methods: ['GET'])]
    public function show(CategorieRessource $categorieRessource): Response
    {
        return $this->render('categorie_ressource/show.html.twig', [
            'categorie_ressource' => $categorieRessource,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_ressource_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieRessource $categorieRessource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieRessourceType::class, $categorieRessource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_ressource_index');
        }

        return $this->render('categorie_ressource/edit.html.twig', [
            'categorie_ressource' => $categorieRessource,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_ressource_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieRessource $categorieRessource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorieRessource->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorieRessource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_ressource_index');
    }
}
