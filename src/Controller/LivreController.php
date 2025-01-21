<?php
// src/Controller/LivreController.php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\EditLivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Afficher la liste des livres avec recherche
    #[Route('/livres', name: 'app_livres')]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {
        $query = $request->query->get('query', ''); 
        if ($query) {
            $livres = $livreRepository->findBySearch($query);
        } else {
            $livres = $livreRepository->findAll();
        }

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'query' => $query, 
        ]);
    }

    // Afficher les détails d'un livre
    #[Route('/livre/{id}', name: 'app_livre_show')]
    public function show(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);
        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouvé');
        }
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    // Modifier un livre
    #[Route('/livre/{id}/modifier', name: 'app_livre_edit')]
    public function edit(int $id, Request $request, LivreRepository $livreRepository): Response
    {
    $livre = $livreRepository->find($id);
    if (!$livre) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    $form = $this->createForm(EditLivreType::class, $livre);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush();

        return $this->redirectToRoute('app_livres');
    }

    return $this->render('livre/edit.html.twig', [
        'form' => $form->createView(),
        'livre' => $livre,  
    ]);
}


    // Supprimer un livre
    #[Route('/livre/{id}/supprimer', name: 'app_livre_delete')]
    public function delete(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);
        if ($livre) {
            $this->entityManager->remove($livre);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_livres');
    }
}
