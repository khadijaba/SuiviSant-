<?php

namespace App\Controller;

use App\Entity\ProgrammeSante;
use App\Form\ParticipationType;
use App\Repository\ProgrammeSanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProgrammeUserController extends AbstractController
{
    #[Route('/programme/user', name: 'app_programme_user')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupération des programmes depuis la base de données
        $programmes = $em->getRepository(ProgrammeSante::class)->findAll();

        return $this->render('programme_user/index.html.twig', [
            'programme_santes' => $programmes,
        ]);
    }

    #[Route('/programme/qr/{id}', name: 'app_programme_qr')]
    public function generateQrCode(int $id, EntityManagerInterface $em): Response
    {
        // Vérifier si le programme existe
        $programmeSante = $em->getRepository(ProgrammeSante::class)->find($id);
        if (!$programmeSante) {
            throw new NotFoundHttpException("Programme non trouvé pour l'ID $id");
        }

        // Générer l'URL du programme
        $url = $this->generateUrl('app_programme_sante_show', ['id' => $programmeSante->getId()], true);

        // ✅ Génération du QR Code
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Retourner l'image en réponse HTTP
        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }

    #[Route('/programme/rate/{id}/{rating}', name: 'app_programme_rate', methods: ['POST'])]
    public function rate(int $id, int $rating, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier si le programme existe
        $programmeSante = $em->getRepository(ProgrammeSante::class)->find($id);
        if (!$programmeSante) {
            return new JsonResponse(["error" => "Programme non trouvé"], 404);
        }

        // Vérifier si la note est valide
        if ($rating < 1 || $rating > 5) {
            return new JsonResponse(["error" => "Valeur de notation invalide"], 400);
        }

        // Mise à jour de la note du programme (Assurez-vous que la méthode `setRating()` existe)
        if (method_exists($programmeSante, 'setRating')) {
            $programmeSante->setRating($rating);
            $em->persist($programmeSante);
            $em->flush();
        } else {
            return new JsonResponse(["error" => "Impossible de noter ce programme"], 500);
        }

        return new JsonResponse(["success" => true, "rating" => $rating]);
    }

    #[Route('/programme/{id}/participer', name: 'programme_user_participer')]
    public function participer(
        int $id, 
        ProgrammeSanteRepository $programmeSanteRepository, 
        Request $request, 
        EntityManagerInterface $em
    ): Response {
        // Recherche du programme
        $programme = $programmeSanteRepository->find($id);

        if (!$programme) {
            throw $this->createNotFoundException("Programme non trouvé");
        }

        // Création du formulaire de participation
        $form = $this->createForm(ParticipationType::class);
        $form->handleRequest($request);

        // Traitement du formulaire après soumission
        if ($form->isSubmitted() && $form->isValid()) {
            $participation = $form->getData();
            // Associer la participation au programme
            $participation->setProgramme($programme);

            // Sauvegarde dans la base de données
            $em->persist($participation);
            $em->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre participation a été enregistrée avec succès.');

            return $this->redirectToRoute('app_programme_user');
        }

        return $this->render('programme_user/participer.html.twig', [
            'programme' => $programme,
            'form' => $form->createView(), // 🔹 Transmettre le formulaire à la vue
        ]);
    }
}
