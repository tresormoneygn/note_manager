<?php

namespace App\Service;

use App\Repository\DepartementHistoriqueRepository;
use App\Repository\DepartementRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NoteRepository;

class StatistiquesService
{
    public function __construct(
        private DepartementHistoriqueRepository $departementRepo,
        private InscriptionRepository $inscriptionRepo,
        private NoteRepository $noteRepo,
    ) {}

    public function genererStatistiques(): array
    {
        $stats = [];
        $departements = $this->departementRepo->findAll();

        foreach ($departements as $departement) {
            $programmes = $departement->getProgrammes();

            foreach ($programmes as $programme) {
                dd($programme);
                foreach ($programme->getClasse() as $classe) {
                    $niveau = $departement->getNom() . ' ' . $classe->getNom();

                    $inscriptions = $this->inscriptionRepo->findBy([
                        'programme' => $programme,
                        'classe' => $classe
                    ]);

                    $totalInscrits = count($inscriptions);
                    $fillesInscrites = 0;
                    $totalEvalues = 0;
                    $fillesEvalues = 0;
                    $admis = 0;
                    $fillesAdmis = 0;
                    $coursNonValides = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, '>=5' => 0];

                    foreach ($inscriptions as $inscription) {
                        $etudiant = $inscription->getEtudiant();
                        $isFille = strtoupper($etudiant->getSexe()) === 'F';

                        if ($isFille) $fillesInscrites++;

                        $notes = $etudiant->getNotes();
                        $nbMatiere = 0;
                        $nbValide = 0;
                        $nbInvalide = 0;
                        $estEvalue = false;

                        foreach ($notes as $note) {
                            $n1 = $note->getNote1();
                            $n2 = $note->getNote2();
                            $n3 = $note->getNote3();

                            if ($n1 != 0 || $n2 != 0 || $n3 != 0) {
                                $estEvalue = true;
                                $moyenne = ($n1 + $n2 + $n3) / 3;
                                if ($moyenne >= 10) {
                                    $nbValide++;
                                } else {
                                    $nbInvalide++;
                                }
                                $nbMatiere++;
                            }
                        }

                        if ($estEvalue) {
                            $totalEvalues++;
                            if ($isFille) $fillesEvalues++;

                            if ($nbMatiere > 0 && $nbValide / $nbMatiere >= 0.5) {
                                $admis++;
                                if ($isFille) $fillesAdmis++;
                            }

                            $coursNonValides[$nbInvalide] = ($coursNonValides[$nbInvalide] ?? 0) + 1;
                            if ($nbInvalide >= 5) {
                                $coursNonValides['>=5']++;
                            }
                        }
                    }

                    $stats[$niveau] = [
                        'effectif_inscrit' => [
                            'total' => $totalInscrits,
                            'filles' => $fillesInscrites
                        ],
                        'effectif_evalue' => [
                            'total' => $totalEvalues,
                            'filles' => $fillesEvalues
                        ],
                        'admis' => [
                            'nbre_admis' => $admis,
                            'filles_admises' => $fillesAdmis,
                            'pourcentage_total' => $totalInscrits > 0 ? round(($admis / $totalInscrits) * 100, 2) : 0,
                            'pourcentage_filles' => $fillesInscrites > 0 ? round(($fillesAdmis / $fillesInscrites) * 100, 2) : 0
                        ],
                        'cours_non_valides' => $coursNonValides,
                        'abandons' => [
                            'total' => 0, // à compléter selon ta logique métier
                            'filles' => 0
                        ]
                    ];
                }
            }
        }

        return $stats;
    }
}
