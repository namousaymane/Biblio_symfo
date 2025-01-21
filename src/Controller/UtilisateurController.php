<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\EditUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route pour afficher la liste des utilisateurs
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function index(): Response
    {
        $utilisateurs = $this->entityManager->getRepository(Utilisateur::class)->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    // Route pour ajouter un utilisateur
    #[Route('/utilisateur/add', name: 'admin_user_add')]
    public function add(Request $request): Response
    {
        $utilisateur = new Utilisateur();

        $form = $this->createForm(EditUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès');
            return $this->redirectToRoute('app_utilisateur');
        }

        return $this->render('utilisateur/add.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    // Route pour modifier un utilisateur
    #[Route('/utilisateur/edit/{id}', name: 'admin_user_edit')]
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(EditUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_utilisateur');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    // Route pour supprimer un utilisateur
    #[Route('/utilisateur/delete/{id}', name: 'admin_user_delete')]
    public function delete(Utilisateur $utilisateur): Response
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès');
        return $this->redirectToRoute('app_utilisateur');
    }
}

