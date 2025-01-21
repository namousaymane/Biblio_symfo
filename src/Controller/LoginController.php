<?php

namespace App\Controller;

use App\Form\UserLoginType;
use App\Form\AdminLoginType;
use App\Entity\Utilisateur;
use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'app_login')]
    public function index(Request $request): Response
    {
        // Formulaire pour l'utilisateur
        $utilisateur = new Utilisateur();
        $userForm = $this->createForm(UserLoginType::class, $utilisateur);
        $userForm->handleRequest($request);

        // Formulaire pour l'admin
        $admin = new Admin();
        $adminForm = $this->createForm(AdminLoginType::class, $admin);
        $adminForm->handleRequest($request);

        // Si le formulaire utilisateur est soumis et valide
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $data = $userForm->getData();
            $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $data->getEmail()]);
            
            if ($user && $user->getMotDePasse() === $data->getMotDePasse()) {
                return $this->redirectToRoute('app_utilisateur_dashboard');
            } else {
                $this->addFlash('user_login_error', 'Identifiants incorrects pour l\'utilisateur');
            }
        }
        
        // Si le formulaire administrateur est soumis et valide
        if ($adminForm->isSubmitted() && $adminForm->isValid()) {
            $data = $adminForm->getData();
            $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['pseudo' => $data->getPseudo()]);
            
            if ($admin && $admin->getMotDePasse() === $data->getMotDePasse()) {
                return $this->redirectToRoute('admin_dashboard');
            } else {
                $this->addFlash('admin_login_error', 'Identifiants incorrects pour l\'administrateur');
            }
        }


        // Si le formulaire est soumis mais pas valide on redirige
        if ($userForm->isSubmitted() || $adminForm->isSubmitted()) {
            return $this->redirectToRoute('app_login'); 
        }

        return $this->render('login/index.html.twig', [
            'userForm' => $userForm->createView(),
            'adminForm' => $adminForm->createView(),
        ]);
    }
}

