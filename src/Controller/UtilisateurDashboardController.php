<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Emprunt;
use App\Repository\LivreRepository;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurDashboardController extends AbstractController
{
    private $entityManager;
    private $livreRepository;
    private $empruntRepository;

    public function __construct(EntityManagerInterface $entityManager, LivreRepository $livreRepository, EmpruntRepository $empruntRepository)
    {
        $this->entityManager = $entityManager;
        $this->livreRepository = $livreRepository;
        $this->empruntRepository = $empruntRepository;
    }

    #[Route('/utilisateur/dashboard', name: 'app_utilisateur_dashboard')]
    public function index(Request $request): Response
    {
        // Récupérer la liste des livres
        $livres = $this->livreRepository->findAll();

        // Gérer l'emprunt d'un livre
        if ($request->isMethod('POST')) {
            $livreId = $request->request->get('livre_id');
            $livre = $this->livreRepository->find($livreId);

            if ($livre && !$livre->getEmprunte()) {
                $emprunt = new Emprunt();
                $emprunt->setUtilisateur($this->getUser());
                $emprunt->setLivre($livre);
                $emprunt->setDateEmprunt(new \DateTime());
                $livre->setEmprunte(true); 

                $this->entityManager->persist($emprunt);
                $this->entityManager->flush();

                $this->addFlash('success', 'Livre emprunté avec succès!');
            } else {
                $this->addFlash('error', 'Le livre est déjà emprunté ou introuvable.');
            }

            return $this->redirectToRoute('app_utilisateur_dashboard');
        }

        return $this->render('utilisateur_dashboard/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/utilisateur/dashboard/retour/{id}', name: 'app_utilisateur_retour')]
    public function retour(Livre $livre): Response
    {
        $emprunt = $this->empruntRepository->findOneBy([
            'utilisateur' => $this->getUser(),
            'livre' => $livre,
            'dateRetour' => null
        ]);

        if ($emprunt) {
            $emprunt->setDateRetour(new \DateTime());
            $livre->setEmprunte(false); 

            $this->entityManager->flush();

            $this->addFlash('success', 'Livre retourné avec succès!');
        } else {
            $this->addFlash('error', 'Vous n\'avez pas emprunté ce livre.');
        }

        return $this->redirectToRoute('app_utilisateur_dashboard');
    }
}
