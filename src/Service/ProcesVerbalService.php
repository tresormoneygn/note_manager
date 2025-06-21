<?php

namespace App\Service;

use App\Repository\InscriptionRepository;
use App\Repository\NoteRepository;
use App\Repository\AnneeRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProcesVerbalService
{
    private $inscriptionRepository;
    private $noteRepository;
    private $anneeRepository;

    public function __construct(
        InscriptionRepository $inscriptionRepository,
        NoteRepository $noteRepository,
        AnneeRepository $anneeRepository
    ) {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->noteRepository = $noteRepository;
        $this->anneeRepository = $anneeRepository;
    }

    public function genererProcesVerbalExcel($anneeString = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configuration de la page
        $sheet->setTitle('Procès Verbal');

        // En-têtes principales (ligne 4)
        $headers = [
            'A4' => 'Niveaux',
            'B4' => 'Eff.Inscrit', 'C4' => '',
            'D4' => 'Eff.Evalué', 'E4' => '',
            'F4' => 'Admis', 'G4' => '', 'H4' => '', 'I4' => '',
            'J4' => 'Nbre cours non Validés', 'K4' => '', 'L4' => '', 'M4' => '', 'N4' => '',
            'O4' => 'Abandons', 'P4' => ''
        ];

        // Sous-en-têtes (ligne 5)
        $subHeaders = [
            'B5' => 'Total', 'C5' => 'Filles',
            'D5' => 'Total', 'E5' => 'Filles',
            'F5' => 'Nbre Admis', 'G5' => 'Fille Admise', 'H5' => '% total', 'I5' => '% filles',
            'J5' => '1', 'K5' => '2', 'L5' => '3', 'M5' => '4', 'N5' => '≥5',
            'O5' => 'N Total', 'P5' => 'NF'
        ];

        // Appliquer les en-têtes
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        foreach ($subHeaders as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Fusionner les cellules pour les en-têtes principaux
        $sheet->mergeCells('A4:A5'); // Niveaux
        $sheet->mergeCells('B4:C4'); // Eff.Inscrit
        $sheet->mergeCells('D4:E4'); // Eff.Evalué
        $sheet->mergeCells('F4:I4'); // Admis
        $sheet->mergeCells('J4:N4'); // Nbre cours non Validés
        $sheet->mergeCells('O4:P4'); // Abandons

        // Récupérer l'année en cours ou utiliser celle fournie
        if (!$anneeString) {
            $anneeEnCours = $this->anneeRepository->findOneBy(['is_progress' => true]);
            $anneeString = $anneeEnCours ? $anneeEnCours->getName() : date('Y');
        }

        // Récupérer toutes les inscriptions pour l'année
        $inscriptions = $this->inscriptionRepository->createQueryBuilder('i')
            ->join('i.annee', 'a')
            ->where('a.name = :annee')
            ->setParameter('annee', $anneeString)
            ->getQuery()
            ->getResult();

        // Grouper par niveau (Programme + Classe)
        $niveaux = [];
        foreach ($inscriptions as $inscription) {
            $programme = $inscription->getProgramme();
            $classe = $inscription->getClasse();
            $niveau = $programme->getLabel() . ' L' . $classe->getName();
            
            if (!isset($niveaux[$niveau])) {
                $niveaux[$niveau] = [];
            }
            $niveaux[$niveau][] = $inscription;
        }

        $row = 6; // Commencer à la ligne 6 pour les données

        foreach ($niveaux as $niveau => $inscriptionsNiveau) {
            $stats = $this->calculerStatistiquesNiveau($inscriptionsNiveau);
            
            // Remplir les données
            $sheet->setCellValue("A$row", $niveau);
            $sheet->setCellValue("B$row", $stats['total_inscrits']);
            $sheet->setCellValue("C$row", $stats['filles_inscrites']);
            $sheet->setCellValue("D$row", $stats['total_evalues']);
            $sheet->setCellValue("E$row", $stats['filles_evaluees']);
            $sheet->setCellValue("F$row", $stats['admis']);
            $sheet->setCellValue("G$row", $stats['filles_admises']);
            $sheet->setCellValue("H$row", $stats['pourcentage_total']);
            $sheet->setCellValue("I$row", $stats['pourcentage_filles']);
            $sheet->setCellValue("J$row", $stats['cours_non_valides'][1] ?? 0);
            $sheet->setCellValue("K$row", $stats['cours_non_valides'][2] ?? 0);
            $sheet->setCellValue("L$row", $stats['cours_non_valides'][3] ?? 0);
            $sheet->setCellValue("M$row", $stats['cours_non_valides'][4] ?? 0);
            $sheet->setCellValue("N$row", $stats['cours_non_valides']['>=5'] ?? 0);
            $sheet->setCellValue("O$row", $stats['abandons_total']);
            $sheet->setCellValue("P$row", $stats['abandons_filles']);

            $row++;
        }

        // Appliquer le style
        $this->appliquerStyle($sheet, $row - 1);

        return $spreadsheet;
    }

    private function calculerStatistiquesNiveau($inscriptions)
    {
        $stats = [
            'total_inscrits' => count($inscriptions),
            'filles_inscrites' => 0,
            'total_evalues' => 0,
            'filles_evaluees' => 0,
            'admis' => 0,
            'filles_admises' => 0,
            'pourcentage_total' => 0,
            'pourcentage_filles' => 0,
            'cours_non_valides' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, '>=5' => 0],
            'abandons_total' => 0,
            'abandons_filles' => 0
        ];

        foreach ($inscriptions as $inscription) {
            $etudiant = $inscription->getEtudiant();
            $isFille = strtoupper($etudiant->getSexe()) === 'F';
            
            if ($isFille) {
                $stats['filles_inscrites']++;
            }

            // Récupérer les notes de l'étudiant
            $notes = $this->noteRepository->findBy(['student' => $etudiant]);
            
            if (empty($notes)) {
                // Étudiant sans notes = abandon
                $stats['abandons_total']++;
                if ($isFille) {
                    $stats['abandons_filles']++;
                }
                continue;
            }

            // Vérifier si l'étudiant a été évalué (au moins une note > 0)
            $estEvalue = false;
            $moyennes = [];
            $coursNonValides = 0;

            foreach ($notes as $note) {
                if ($note->getNote1() > 0 || $note->getNote2() > 0 || $note->getNote3() > 0) {
                    $estEvalue = true;
                    $moyenne = ($note->getNote1() * 0.3) + ($note->getNote2() * 0.3) + ($note->getNote3() * 0.4);
                    $moyennes[] = $moyenne;
                    
                    if ($moyenne < 10) {
                        $coursNonValides++;
                    }
                }
            }

            if (!$estEvalue) {
                // Pas de notes valides = abandon
                $stats['abandons_total']++;
                if ($isFille) {
                    $stats['abandons_filles']++;
                }
                continue;
            }

            $stats['total_evalues']++;
            if ($isFille) {
                $stats['filles_evaluees']++;
            }

            // Calculer la moyenne générale
            if (!empty($moyennes)) {
                $moyenneGenerale = array_sum($moyennes) / count($moyennes);
                
                // Vérifier si admis (moyenne >= 10)
                if ($moyenneGenerale >= 10) {
                    $stats['admis']++;
                    if ($isFille) {
                        $stats['filles_admises']++;
                    }
                }
            }

            // Compter les cours non validés
            if ($coursNonValides >= 5) {
                $stats['cours_non_valides']['>=5']++;
            } elseif ($coursNonValides > 0) {
                $stats['cours_non_valides'][$coursNonValides]++;
            }
        }

        // Calculer les pourcentages
        if ($stats['total_inscrits'] > 0) {
            $stats['pourcentage_total'] = round(($stats['admis'] / $stats['total_inscrits']) * 100, 1);
        }
        
        if ($stats['filles_inscrites'] > 0) {
            $stats['pourcentage_filles'] = round(($stats['filles_admises'] / $stats['filles_inscrites']) * 100, 1);
        }

        return $stats;
    }

    private function appliquerStyle($sheet, $lastRow)
    {
        // Style pour les en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E6E6FA']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $sheet->getStyle('A4:P5')->applyFromArray($headerStyle);

        // Style pour les données
        $dataStyle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $sheet->getStyle("A6:P$lastRow")->applyFromArray($dataStyle);

        // Ajuster la largeur des colonnes
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Hauteur des lignes
        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(20);
    }
}