<?php

namespace App\Controller;

use App\Service\StatistiquesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Service\ExcelExportService;

class StatistiquesController extends AbstractController
{
    #[Route('/api/statistiques', name: 'app_statistiques')]
    public function index(StatistiquesService $statistiquesService): JsonResponse
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
