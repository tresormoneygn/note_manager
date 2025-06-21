<?php

namespace App\Controller;

use App\Repository\AnneeRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;
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
    public function home(InscriptionRepository $inscriptionRepository, AnneeRepository $anneeRepository): Response
    {
        $annee = $anneeRepository->findOneBy(['is_progress' => true]);
        $nb_students = $inscriptionRepository->count(['annee' => $annee]);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'active_page' => 'dashboard',
            'student_count' => $nb_students
        ]);
    }

    
}
