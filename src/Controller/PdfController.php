<?php
namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RessourceEducativeRepository;
class PdfController extends AbstractController
{
    
    #[Route('/generate-pdf', name: 'generate_pdf', methods: ['GET'])]
    public function generatePdf(RessourceEducativeRepository $ressourceEducativeRepository): Response
    {
        $ressources = $ressourceEducativeRepository->findAll();
    
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
    
        $html = $this->renderView('pdf/ressources.html.twig', [
            'ressources' => $ressources,
        ]);
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="ressources.pdf"',
            ]
        );
    }
}
    