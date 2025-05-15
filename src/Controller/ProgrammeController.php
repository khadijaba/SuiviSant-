<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/programme')]
class ProgrammeController extends AbstractController
{
    #[Route('/', name: 'app_programme_index', methods: ['GET'])]
    public function index(ProgrammeRepository $programmeRepository): Response
    {
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_programme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $programme = new Programme();
            $programme->setNom($request->request->get('nom'))
                     ->setDate(new \DateTime($request->request->get('date')))
                     ->setProgression($request->request->get('progression'))
                     ->setEvaluation((int)$request->request->get('evaluation'))
                     ->setLikes(0)
                     ->setDislikes(0);

            $errors = $validator->validate($programme);
            if (count($errors) > 0) {
                return $this->render('programme/new.html.twig', [
                    'errors' => $errors,
                    'programme' => $programme,
                ]);
            }

            $entityManager->persist($programme);
            $entityManager->flush();

            $this->addFlash('success', 'Programme créé avec succès!');
            return $this->redirectToRoute('app_programme_index');
        }

        return $this->render('programme/new.html.twig');
    }

    #[Route('/{nom}', name: 'app_programme_show', methods: ['GET'])]
    public function show(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
    }

    #[Route('/{nom}/edit', name: 'app_programme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Programme $programme, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $programme->setProgression($request->request->get('progression'))
                     ->setEvaluation((int)$request->request->get('evaluation'));

            $errors = $validator->validate($programme);
            if (count($errors) > 0) {
                return $this->render('programme/edit.html.twig', [
                    'errors' => $errors,
                    'programme' => $programme,
                ]);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Programme mis à jour avec succès!');
            return $this->redirectToRoute('app_programme_show', ['nom' => $programme->getNom()]);
        }

        return $this->render('programme/edit.html.twig', [
            'programme' => $programme,
        ]);
    }

    #[Route('/{nom}', name: 'app_programme_delete', methods: ['POST'])]
    public function delete(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programme->getNom(), $request->request->get('_token'))) {
            $entityManager->remove($programme);
            $entityManager->flush();
            $this->addFlash('success', 'Programme supprimé avec succès!');
        }

        return $this->redirectToRoute('app_programme_index');
    }

    #[Route('/{nom}/like', name: 'app_programme_like', methods: ['POST'])]
    public function like(Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $programme->setLikes($programme->getLikes() + 1);
        $entityManager->flush();
        
        return $this->json([
            'likes' => $programme->getLikes(),
            'rating' => $programme->getRating()
        ]);
    }

    #[Route('/{nom}/dislike', name: 'app_programme_dislike', methods: ['POST'])]
    public function dislike(Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $programme->setDislikes($programme->getDislikes() + 1);
        $entityManager->flush();
        
        return $this->json([
            'dislikes' => $programme->getDislikes(),
            'rating' => $programme->getRating()
        ]);
    }
} 