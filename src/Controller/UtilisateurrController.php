<?php

namespace App\Controller;

use App\Entity\Utilisateurr;
use App\Form\UtilisateurrType;
use App\Repository\UtilisateurrRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/utilisateurr')]
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
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateurr = new Utilisateurr();
        $form = $this->createForm(UtilisateurrType::class, $utilisateurr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔒 Hachage du mot de passe AVANT l'enregistrement
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateurr, $plainPassword);
                $utilisateurr->setPassword($hashedPassword);
            }

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
    public function edit(Request $request, Utilisateurr $utilisateurr, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UtilisateurrType::class, $utilisateurr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔄 Mise à jour sécurisée du mot de passe SEULEMENT si un nouveau mot de passe est fourni
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateurr, $plainPassword);
                $utilisateurr->setPassword($hashedPassword);
            }

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
        // ✅ Correction : Utilisation de request->request->get('_token') au lieu de getPayload()->getString('_token')
        if ($this->isCsrfTokenValid('delete'.$utilisateurr->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateurr);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateurr_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/utilisateur/rechercher', name: 'app_utilisateur_rechercher', methods: ['POST'])]
    public function rechercher(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $nom = $request->request->get('nom'); 
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('u')
            ->from(Utilisateurr::class, 'u')
            ->where('u.nom LIKE :nom') 
            ->setParameter('nom', '%' . $nom . '%') 
            ->orderBy('u.nom', 'ASC'); 
        $utilisateurs = $queryBuilder->getQuery()->getResult(); 

        $utilisateursArray = []; 
        foreach ($utilisateurs as $utilisateur) {
            $utilisateursArray[] = [
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(), 
                'prenom' => $utilisateur->getPrenom(), 
                'email' => $utilisateur->getEmail(), 
                'dateNaissance' => $utilisateur->getDateNaissance(),
                'sexe' => $utilisateur->getSexe(),
            ];
        }

        
        return new JsonResponse([
            'utilisateurs' => $utilisateursArray
        ]);
    }
}
