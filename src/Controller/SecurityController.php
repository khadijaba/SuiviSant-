<?php

namespace App\Controller;

use App\Entity\Utilisateurr;
use App\Form\ForgetPasswordType;
use App\Form\PasswordChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $mailer;
    private  $entityManager;
    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifier si l'utilisateur est déjà connecté
        $user = $this->getUser();
    
        if ($user) {
            // Vérifier les rôles et rediriger
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_dashboard'); // Route pour l'admin
            }
            return $this->redirectToRoute('app_home'); // Route pour l'utilisateur normal
        }
    
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }
    

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Géré automatiquement par Symfony, pas besoin de code ici
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/send')]
    public function send (){
        $email = (new Email())
        ->from('freeelanci@gmail.com')
        ->to('mbenjamaia@gmail.com')
        ->subject('Reset Password')
        ->html(
            'aaaaaaaaaa'
        );
        try {
            $this->mailer->send($email);
            return new Response('Email envoyé avec succès');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
    

    #[Route('/api/auth/user', name: 'api_auth_user', methods: ['GET'])]
    public function getUserInfo(): JsonResponse
    {
        // Vérifie si l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        return $this->json([
            'username' => $user->getUserIdentifier(), // Adapter selon ton entité User
            
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            
            $user = $this->entityManager->getRepository(Utilisateurr::class)
                ->findOneBy(['email' => $email]);
            dump($user);
            if (!$user) {
                $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet email.');
                return $this->redirectToRoute('app_forgot_password');
            }
        
            // Générer un token sécurisé
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
        
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur lors de la génération du token.');
                return $this->redirectToRoute('app_forgot_password');
            }
        
            // Créer et envoyer l'e-mail de réinitialisation
            $emailMessage = (new Email())
                ->from('freeelanci@gmail.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->html($this->renderView('security/reset_password_email.html.twig', [
                    'user' => $user,
                    'token' => $token,
                ]));
        
            try {
                $this->mailer->send($emailMessage);
                $this->addFlash('success', 'Un e-mail de réinitialisation a été envoyé.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'e-mail.');
            }
        
            // Rediriger l'utilisateur après la soumission du formulaire
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')]
    public function resetpassword(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    ) {
        // Find the user by the reset token
        $user = $entityManager->getRepository(Utilisateurr::class)->findOneBy(['resetToken' => $token]);
    
        // If no user is found with the token, redirect to the login page with an error message
        if ($user === null) {
            $this->addFlash('danger', 'Token invalide ou expiré.');
            $logger->error('Invalid or expired token used for password reset: {token}', ['token' => $token]);
            return $this->redirectToRoute('app_login');
        }
    
        // Create the password change form
        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);
    
        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the new password from the form
            $newPassword = $form->get('password')->getData();
    
            // Hash the new password
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
    
            // Update the user's password and clear the reset token
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
    
            // Save the updated user entity
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Notify the user of success and redirect to the login page
            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            return $this->redirectToRoute('app_login');
        }
    
        // Render the password change form
        return $this->render('security/passwordchange.html.twig', [
            'form' => $form->createView(),
            'token' => $token,
        ]);
    }
}


