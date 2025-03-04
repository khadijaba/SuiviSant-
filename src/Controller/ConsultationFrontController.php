<?php


namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rapport;
use Knp\Snappy\Pdf;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use App\Entity\Consultation;
use App\Repository\ConsultationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
final class ConsultationFrontController extends AbstractController
{
    #[Route('/consultation/front', name: 'app_consultation_front')]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findAll();
    
        return $this->render('consultation_front/index.html.twig', [
            'consultations' => $consultations,
        ]);
    }


    #[Route('/consultation/front/{id}', name: 'app_consultation_front_show', methods: ['GET'])]
    public function show(Consultation $consultation, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        // Récupérer tous les rapports de la consultation
        $rapports = $em->getRepository(Rapport::class)->findBy(
            ['consultation' => $consultation],
            ['date' => 'DESC']
        );
    
        // Liste des sections après découpage
        $sections = [];
    
        // Diviser chaque rapport en trois parties et appliquer le formatage
        foreach ($rapports as $rapport) {
            $contenu = $rapport->getContenu();
            $contenu = $this->formatRapportContent($contenu); // Appliquer le formatage
            $sections = array_merge($sections, $this->splitContentIntoThree($contenu));
        }
    
        // Pagination des sections du contenu
        $paginationSections = $paginator->paginate(
            $sections,  // Liste des sections à paginer
            $request->query->getInt('page', 1),  // Numéro de la page
            1  // Une section par page
        );
    
        return $this->render('consultation_front/show.html.twig', [
            'consultation' => $consultation,
            'pagination' => $paginationSections,  // Passe la pagination des sections
        ]);
    }
    
    // 🔹 Fonction pour formater le contenu du rapport
    private function formatRapportContent(string $contenu): string
    {
        // Ajouter un saut de ligne après chaque point suivi d'un texte
        $contenu = preg_replace('/([^\n])\./', '$1.<br>', $contenu);
    
        // Ajouter un saut de ligne après chaque titre suivi de ":"
        $contenu = preg_replace('/([^\n:]+:)\s*/', '<strong>$1</strong><br>', $contenu);
    
        return $contenu;
    }
    
    // 🔹 Fonction pour découper le contenu en trois parties égales
    private function splitContentIntoThree(string $contenu): array
    {
        $sections = [];
        $length = strlen($contenu);
    
        // Vérifier si le contenu est suffisant pour être divisé
        if ($length < 3) {
            return [$contenu]; // Retourner le contenu complet si trop court
        }
    
        // Diviser en trois parties égales
        $sectionLength = (int) ceil($length / 3);
    
        $sections[] = substr($contenu, 0, $sectionLength);
        $sections[] = substr($contenu, $sectionLength, $sectionLength);
        $sections[] = substr($contenu, 2 * $sectionLength);
    
        return $sections;
    }
    #[Route('/{id}/download-rapport', name: 'app_consultation_download_rapport', methods: ['GET'])]
    public function downloadRapport(Consultation $consultation): Response
    {
        // Vérifier si un rapport existe
        $rapport = $consultation->getRapport();
        if (!$rapport) {
            $this->addFlash('error', 'Aucun rapport disponible pour cette consultation.');
            return $this->redirectToRoute('app_consultation_front_show', ['id' => $consultation->getId()]);
        }

        // Générer le contenu HTML du rapport
        $htmlContent = $this->renderView('consultation_front/rapport_pdf.html.twig', [
            'rapport' => $rapport,
            'consultation' => $consultation,
        ]);

        // Configurer DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Charger et rendre le HTML
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner le PDF au navigateur
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="rapport_'.$consultation->getId().'.pdf"'
            ]
        );
    }
}    