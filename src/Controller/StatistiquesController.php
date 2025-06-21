<?php

namespace App\Controller;

use App\Service\StatistiquesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Service\ExcelExportService;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques_dashboard')]
    public function dashboard(StatistiquesService $statistiquesService): Response
    {
        // Récupérer les statistiques globales
        $statistiquesGlobales = $statistiquesService->getStatistiquesGlobales();

        return $this->render('statistiques/index.html.twig', [
            'moyenne_generale' => $statistiquesGlobales['moyenne_generale'],
            'repartition_notes' => $statistiquesGlobales['repartition_notes']
        ]);
    }

    #[Route('/api/statistiques', name: 'app_statistiques_api')]
    public function getStatistiques(StatistiquesService $statistiquesService): JsonResponse
    {
        $stats = $statistiquesService->genererStatistiques();
        return $this->json($stats);
    }

    #[Route('/telecharger-statistiques', name: 'telecharger_statistiques')]
    public function telecharger(
        StatistiquesService $statistiquesService,
        ExcelExportService $excelExportService
    ): StreamedResponse {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '1024M');

        $data = $statistiquesService->genererStatistiques();
        $spreadsheet = $excelExportService->generate($data);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'statistiques.xlsx'
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
