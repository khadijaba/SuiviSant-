<?php

namespace App\Controller;

use App\Entity\Expert;
use App\Form\ExpertType;
use App\Repository\ExpertRepository;
use App\Repository\DispoExpertRepository; // ✅ Corrected import
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/expert')]
final class ExpertController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private DispoExpertRepository $dispoExpertRepository;

    public function __construct(EntityManagerInterface $entityManager, DispoExpertRepository $dispoExpertRepository)
    {
        $this->entityManager = $entityManager;
        $this->dispoExpertRepository = $dispoExpertRepository;
    }

    #[Route(name: 'app_expert_index', methods: ['GET'])]
    public function index(ExpertRepository $expertRepository): Response
    {
        return $this->render('expert/index.html.twig', [
            'experts' => $expertRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_expert_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $expert = new Expert();
        $form = $this->createForm(ExpertType::class, $expert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFileUpload($form, $expert, $slugger);
            $this->entityManager->persist($expert);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_expert_index');
        }

        return $this->render('expert/new.html.twig', [
            'expert' => $expert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_expert_show', methods: ['GET'])]
    public function show(Expert $expert): Response
    {
        return $this->render('expert/show.html.twig', [
            'expert' => $expert,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expert_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expert $expert, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ExpertType::class, $expert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleFileUpload($form, $expert, $slugger);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_expert_index');
        }

        return $this->render('expert/edit.html.twig', [
            'expert' => $expert,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_expert_delete', methods: ['POST'])]
    public function delete(Request $request, Expert $expert): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expert->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($expert);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_expert_index');
    }

    #[Route('/{id}/dispos', name: 'app_expert_dispos', methods: ['GET'])]
public function showDispos(Expert $expert, DispoExpertRepository $dispoExpertRepository, Request $request): Response
{
    // Check if the request asks for JSON data
    if ($request->query->get('json')) {
        $dispos = $dispoExpertRepository->findBy(['expert' => $expert]);

        $events = [];

        foreach ($dispos as $dispo) {
            $events[] = [
                'title' => 'Available',
                'start' => $dispo->getDate()->format('Y-m-d') . 'T' . $dispo->getTime()->format('H:i'),
                'extendedProps' => [
                    'dispoId' => $dispo->getId(), // ✅ Fix: Ensure dispoId is included
                    'location' => $dispo->getLocation(),
                    'status' => $dispo->getStatus() ? 'Available' : 'Unavailable',
                ],
                'color' => $dispo->getStatus() ? '#28a745' : '#dc3545', // Green for available, Red for unavailable
            ];
        }

        return $this->json($events);
    }

    // Render the availability page
    return $this->render('expert/dispos.html.twig', [
        'expert' => $expert,
    ]);
}

    /**
     * Handles file upload for expert's photo.
     */
    private function handleFileUpload($form, Expert $expert, SluggerInterface $slugger): void
    {
        $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move($this->getParameter('photos_directory'), $newFilename);
                $expert->setPhoto($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Failed to upload image.');
            }
        }
    }
    

    

}
