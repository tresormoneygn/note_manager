<?php

namespace App\Service;

use App\Repository\DepartementHistoriqueRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NoteRepository;
use App\Repository\AnneeRepository;

class StatistiquesService
{
    public function __construct(
        private DepartementHistoriqueRepository $departementRepo,
        private InscriptionRepository $inscriptionRepo,
        private NoteRepository $noteRepo,
        private AnneeRepository $anneeRepo,
    ) {}

    public function genererStatistiques(): array
    {
        $stats = [];
        
        // Récupérer l'année en cours
        $anneeEnCours = $this->anneeRepo->findOneBy(['is_progress' => true]);
        if (!$anneeEnCours) {
            return $stats;
        }

        // Récupérer toutes les inscriptions pour l'année en cours
        $inscriptions = $this->inscriptionRepo->findBy(['annee' => $anneeEnCours]);
        
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

        foreach ($niveaux as $niveau => $inscriptionsNiveau) {
            $totalInscrits = count($inscriptionsNiveau);
            $fillesInscrites = 0;
            $totalEvalues = 0;
            $fillesEvalues = 0;
            $admis = 0;
            $fillesAdmis = 0;
            $coursNonValides = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, '>=5' => 0];
            $repartitionNotes = ['0-5' => 0, '5-10' => 0, '10-15' => 0, '15-20' => 0];

            foreach ($inscriptionsNiveau as $inscription) {
                $etudiant = $inscription->getEtudiant();
                $isFille = strtoupper($etudiant->getSexe()) === 'F';

                if ($isFille) $fillesInscrites++;

                $notes = $etudiant->getNotes();
                $nbMatiere = 0;
                $nbValide = 0;
                $nbInvalide = 0;
                $estEvalue = false;
                $moyenneEtudiant = 0;
                $totalNotes = 0;

                foreach ($notes as $note) {
                    $n1 = $note->getNote1();
                    $n2 = $note->getNote2();
                    $n3 = $note->getNote3();

                    if ($n1 != 0 || $n2 != 0 || $n3 != 0) {
                        $estEvalue = true;
                        $moyenne = ($n1 * 0.3) + ($n2 * 0.3) + ($n3 * 0.4);
                        $moyenneEtudiant += $moyenne;
                        $totalNotes++;
                        
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

                    if ($nbMatiere > 0) {
                        $moyenneFinale = $moyenneEtudiant / $totalNotes;
                        
                        // Répartition des moyennes
                        if ($moyenneFinale < 5) {
                            $repartitionNotes['0-5']++;
                        } elseif ($moyenneFinale < 10) {
                            $repartitionNotes['5-10']++;
                        } elseif ($moyenneFinale < 15) {
                            $repartitionNotes['10-15']++;
                        } else {
                            $repartitionNotes['15-20']++;
                        }

                        // Vérifier si admis (au moins 50% des matières validées)
                        if ($nbMatiere > 0 && ($nbValide / $nbMatiere) >= 0.5) {
                            $admis++;
                            if ($isFille) $fillesAdmis++;
                        }
                    }

                    // Compter les cours non validés
                    if ($nbInvalide > 0) {
                        if ($nbInvalide >= 5) {
                            $coursNonValides['>=5']++;
                        } else {
                            $coursNonValides[$nbInvalide]++;
                        }
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
                'repartition_notes' => $repartitionNotes,
                'abandons' => [
                    'total' => $totalInscrits - $totalEvalues,
                    'filles' => $fillesInscrites - $fillesEvalues
                ]
            ];
        }

        return $stats;
    }

    public function getStatistiquesGlobales(): array
    {
        $stats = $this->genererStatistiques();
        
        $globales = [
            'total_inscrits' => 0,
            'total_evalues' => 0,
            'total_admis' => 0,
            'repartition_notes' => ['0-5' => 0, '5-10' => 0, '10-15' => 0, '15-20' => 0],
            'moyenne_generale' => 0
        ];

        foreach ($stats as $niveau) {
            $globales['total_inscrits'] += $niveau['effectif_inscrit']['total'];
            $globales['total_evalues'] += $niveau['effectif_evalue']['total'];
            $globales['total_admis'] += $niveau['admis']['nbre_admis'];
            
            foreach ($niveau['repartition_notes'] as $range => $count) {
                $globales['repartition_notes'][$range] += $count;
            }
        }

        // Calculer la moyenne générale
        $notes = $this->noteRepo->findAll();
        $totalNotes = 0;
        $sommeNotes = 0;
        
        foreach ($notes as $note) {
            if ($note->getNote1() != 0 || $note->getNote2() != 0 || $note->getNote3() != 0) {
                $moyenne = ($note->getNote1() * 0.3) + ($note->getNote2() * 0.3) + ($note->getNote3() * 0.4);
                $sommeNotes += $moyenne;
                $totalNotes++;
            }
        }

        $globales['moyenne_generale'] = $totalNotes > 0 ? round($sommeNotes / $totalNotes, 2) : 0;

        return $globales;
    }
}