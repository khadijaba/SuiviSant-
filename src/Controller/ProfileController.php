<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        // Vous pouvez obtenir l'utilisateur connecté via $this->getUser()
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
