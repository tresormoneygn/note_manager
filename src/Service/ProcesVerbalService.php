<?php

// src/Service/ProcesVerbalService.php
namespace App\Service;

use App\Repository\InscriptionRepository;
use App\Repository\NoteRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProcesVerbalService
{
    private $inscriptionRepository;
    private $noteRepository;

    public function __construct(
        InscriptionRepository $inscriptionRepository,
        NoteRepository $noteRepository
    ) {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->noteRepository = $noteRepository;
    }

    public function genererProcesVerbalExcel($annee)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Première ligne d'en-tête (catégories)
        $sheet->fromArray([
            'Niveau',
            'Inscrits', '', 
            'Évalués', '', 
            'Admis', '', 
            'Cours non validés', '', '', 
            'Abandons', ''
        ], null, 'A1');

        // Deuxième ligne d'en-tête (sous-colonnes)
        $sheet->fromArray([
            '', 
            'Total', 'Filles',
            'Total', 'Filles',
            'Total', 'Filles',
            '1-2', '3-5', '>5',
            'Total', 'Filles'
        ], null, 'A2');

        // Fusionner les cellules pour les catégories principales
        $sheet->mergeCells('B1:C1'); // Inscrits
        $sheet->mergeCells('D1:E1'); // Évalués
        $sheet->mergeCells('F1:G1'); // Admis
        $sheet->mergeCells('H1:J1'); // Cours non validés
        $sheet->mergeCells('K1:L1'); // Abandons
        $sheet->mergeCells('A1:A2'); // Niveau

        $inscriptions = $this->inscriptionRepository->findByAnnee($annee);

        // Grouper par niveau
        $groupes = [];
        foreach ($inscriptions as $inscription) {
            $niveau = $inscription->getProgramme()->__toString();
            $groupes[$niveau][] = $inscription;
        }

        $row = 3;
        foreach ($groupes as $niveau => $inscriptionsNiveau) {
            // On ne traite que les niveaux avec au moins un inscrit
            if (count($inscriptionsNiveau) === 0) {
                continue;
            }

            $total = count($inscriptionsNiveau);
            $filles = 0;
            $evalTotal = 0;
            $evalFilles = 0;
            $admis = 0;
            $admisFilles = 0;
            $coursNonValides1_2 = 0;
            $coursNonValides3_5 = 0;
            $coursNonValides5plus = 0;
            $abandons = 0;
            $abandonsFilles = 0;

            foreach ($inscriptionsNiveau as $inscription) {
                $etudiant = $inscription->getEtudiant();
                $isFille = strtoupper(substr($etudiant->getSexe(), 0, 1)) === 'F';
                if ($isFille) $filles++;

                $notes = $this->noteRepository->findByEtudiant($etudiant);

                // Un étudiant est considéré abandonné si toutes ses notes sont à zéro ou aucune note
                if (count($notes) === 0 || array_reduce($notes, fn($carry, $note) => $carry && ($note->getMoyenne() == 0), true)) {
                    $abandons++;
                    if ($isFille) $abandonsFilles++;
                    continue;
                }

                $moyennesCours = [];
                $somme = 0;
                $coefTotal = 0;
                foreach ($notes as $note) {
                    $moyenne = $note->getMoyenne();
                    $coef = $note->getMatiere()->getCoefficient();
                    $moyennesCours[] = $moyenne;
                    $somme += $moyenne * $coef;
                    $coefTotal += $coef;
                }

                $moyenneGenerale = $coefTotal > 0 ? $somme / $coefTotal : 0;
                $evalTotal++;
                if ($isFille) $evalFilles++;

                if ($moyenneGenerale >= 5) {
                    $admis++;
                    if ($isFille) $admisFilles++;
                }

                // Cours non validés
                $nbNonValide = count(array_filter($moyennesCours, fn($m) => $m < 5));
                if ($nbNonValide >= 1 && $nbNonValide <= 2) $coursNonValides1_2++;
                elseif ($nbNonValide >= 3 && $nbNonValide <= 5) $coursNonValides3_5++;
                elseif ($nbNonValide > 5) $coursNonValides5plus++;
            }

            $sheet->fromArray([
                $niveau,
                $total, $filles,
                $evalTotal, $evalFilles,
                $admis, $admisFilles,
                $coursNonValides1_2, $coursNonValides3_5, $coursNonValides5plus,
                $abandons, $abandonsFilles
            ], null, "A$row");
            $row++;
        }

        return $spreadsheet;
    }
}
