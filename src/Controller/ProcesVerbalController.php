<?php

namespace App\Controller;

use App\Service\ProcesVerbalService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProcesVerbalController extends AbstractController
{
    #[Route('/proces-verbal/excel', name: 'app_proces_verbal_excel')]
    #[IsGranted('ROLE_DP')]
    public function procesVerbal(ProcesVerbalService $procesVerbalService): Response
    {
        $annee = date('Y'); // ou récupérer dynamiquement depuis la BDD
        $spreadsheet = $procesVerbalService->genererProcesVerbalExcel($annee);

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'proces_verbal');
        $writer->save($temp_file);

        return $this->file($temp_file, "proces-verbal-$annee.xlsx", ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    #[Route('/proces-verbal/{annee}', name: 'app_proces_verbal_excel_annee')]
    #[IsGranted('ROLE_DP')]
    public function exportProcesVerbalExcel(
        $annee,
        ProcesVerbalService $procesVerbalService
    ): StreamedResponse {
        $spreadsheet = $procesVerbalService->genererProcesVerbalExcel($annee);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = "proces_verbal_$annee.xlsx";
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}