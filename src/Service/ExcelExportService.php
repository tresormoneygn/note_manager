<?php

// src/Service/ExcelExportService.php
namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    public function generate(array $data): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->fromArray([
            'Niveau', 'Effectif Inscrit', 'Filles', 'Évalués', 'Filles évaluées',
            'Admis', 'Filles admises', '% total', '% filles',
            'Cours non validés 1', '2', '3', '4', '5', '>=5'
        ], null, 'A1');

        $row = 2;
        foreach ($data as $niveau => $valeurs) {
            $sheet->fromArray([
                $niveau,
                $valeurs['effectif_inscrit']['total'],
                $valeurs['effectif_inscrit']['filles'],
                $valeurs['effectif_evalue']['total'],
                $valeurs['effectif_evalue']['filles'],
                $valeurs['admis']['nbre_admis'],
                $valeurs['admis']['filles_admises'],
                $valeurs['admis']['pourcentage_total'],
                $valeurs['admis']['pourcentage_filles'],
                $valeurs['cours_non_valides'][1] ?? 0,
                $valeurs['cours_non_valides'][2] ?? 0,
                $valeurs['cours_non_valides'][3] ?? 0,
                $valeurs['cours_non_valides'][4] ?? 0,
                $valeurs['cours_non_valides'][5] ?? 0,
                $valeurs['cours_non_valides']['>=5'] ?? 0,
            ], null, 'A' . $row);
            $row++;
        }

        return $spreadsheet;
    }
}
