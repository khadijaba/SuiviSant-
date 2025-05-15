<?php

namespace App\Controller;
use App\Form\EmailFormType;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    #[Route('/send-email', name: 'send_email', methods: ['GET', 'POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
    
            $email = (new Email())
                ->from('benayedkhadija@gmail.com')
                ->to($data['to']) ->to('khadija.benayed@esprit.tn') 
                ->subject('Test Email')
                ->html('<p>Ceci est un test d\'envoi d\'email avec Symfony.</p>'); 
    
            $mailer->send($email);
    
            $this->addFlash('success', 'Email envoyé avec succès !');
            
            // Redirection en fonction du rôle
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_dashboard');
            }
            
            return $this->redirectToRoute('app_utilisateur');
        }
    
        return $this->render('email/send_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

