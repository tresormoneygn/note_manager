<?php

namespace App\Controller;

use App\Repository\AnneeRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use App\Service\StatistiquesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '1024M');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('home', name: 'app_home', methods: ['GET'])]
    public function home(
        InscriptionRepository $inscriptionRepository, 
        AnneeRepository $anneeRepository,
        MatiereRepository $matiereRepository,
        NoteRepository $noteRepository,
        StatistiquesService $statistiquesService
    ): Response {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '1024M');

        $annee = $anneeRepository->findOneBy(['is_progress' => true]);
        $nb_students = $inscriptionRepository->count(['annee' => $annee]);
        $nb_matieres = $matiereRepository->count([]);
        $nb_notes = $noteRepository->count([]);

        // Récupérer les statistiques globales
        $statistiquesGlobales = $statistiquesService->getStatistiquesGlobales();

        // Récupérer les dernières notes
        $dernieresNotes = $noteRepository->findBy([], ['id' => 'DESC'], 10);

        // Calculer les alertes
        $etudiantsSansNotes = [];
        $notesEnRetard = [];

        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'active_page' => 'dashboard',
            'student_count' => $nb_students,
            'matieres_count' => $nb_matieres,
            'notes_count' => $nb_notes,
            'moyenne_generale' => $statistiquesGlobales['moyenne_generale'],
            'dernieres_notes' => $dernieresNotes,
            'repartition_notes' => $statistiquesGlobales['repartition_notes'],
            'etudiants_sans_notes' => $etudiantsSansNotes,
            'notes_en_retard' => $notesEnRetard,
            'statistiques_globales' => $statistiquesGlobales
        ]);
    }
}
