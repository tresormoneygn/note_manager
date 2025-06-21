<?php

namespace App\Controller;

use App\Constant\FileConstant;
use App\Constant\xtnsionConstant;
use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Entity\Rapport;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\MatiereRepository;
use App\Form\FiltreNoteType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/note')]
#[IsGranted('ROLE_P')]
final class NoteController extends AbstractController
{
    #[Route(name: 'app_note_index', methods: ['GET'])]
    public function index(
        Request $request,
        NoteRepository $noteRepository,
        MatiereRepository $matiereRepository,
        Security $security,
        PaginatorInterface $paginator,
    ): Response
    {
        $user = $security->getUser();
        $userMatieres = $matiereRepository->findBy(['user' => $user]);
        
        $form = $this->createForm(FiltreNoteType::class, null, [
            'method' => 'GET',
            'user_matieres' => $userMatieres
        ]);
        $form->handleRequest($request);

        $notes = [];
        $stats = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $matricule = $data['matricule'] ?? null;
            $nom = $data['nom'] ?? null;
            $annee = $data['annee'] ?? null;
            $matiere = $data['matiere'] ?? null;

            $notes = $noteRepository->filtrerNote($matricule, $nom, $annee, $matiere);
        } else {
            // Get only notes for subjects taught by the connected professor
            $notes = $noteRepository->createQueryBuilder('n')
                ->where('n.matiere IN (:matieres)')
                ->setParameter('matieres', $userMatieres)
                ->getQuery()
                ->getResult();
        }

        // Paginer les résultats
        $notes = $paginator->paginate(
            $notes, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page, 1 par défaut
            10, // Nombre d'éléments par page
            options: [
                'defaultSortFieldName' => 'n.id',
                'defaultSortDirection' => 'DESC',
            ]
        );


        $stats = $this->calculateStatistics($notes->getItems() ?? []);
        return $this->render('note/index.html.twig', [
            'form' => $form->createView(),
            'notes' => $notes,
            'matieres' => $userMatieres,
            'intervals' => $stats['intervals'],
            'types' => $stats['types'],
        ]);

        // return $this->render('note/index.html.twig', [
        //     'form' => $form->createView(),
        //     'notes' => $noteRepository->findAll(),
        // ]);




    }

    public function afficherStatistiques(NoteRepository $noteRepository): Response
    {
        $notes = $noteRepository->findAll();
        $stats = $this->calculateStatistics($notes);

        return $this->render('note/index.html.twig', [
            'noteData' => $stats
        ]);
    }


    // Exemple de fonction pour calculer les statistiques des notes
    private function calculateStatistics(array $notes): array
    {
        $intervals = [
            'Moyenne entre 0 et 2.5' => 0,
            'Moyenne entre 2.5 et 5' => 0,
            'Moyenne entre 5 et 7.5' => 0,
            'Moyenne entre 7.5 et 10' => 0
        ];

        $types = [
            'Admis' => 0,
            'Non Admis' => 0
        ];

        foreach ($notes as $note) {
            $moyenne = ($note->getNote1()*0.3 + $note->getNote2()*0.3 + $note->getNote3()*0.4);

            // Répartition par tranches
            if ($moyenne < 2.5) $intervals['Moyenne entre 0 et 2.5']++;
            elseif ($moyenne < 5) $intervals['Moyenne entre 2.5 et 5']++;
            elseif ($moyenne < 7.5) $intervals['Moyenne entre 5 et 7.5']++;
            else $intervals['Moyenne entre 7.5 et 10']++;

            // Répartition par mention
            if ($moyenne < 5) $types['Non Admis']++;
            else $types['Admis']++;
        }

        return [
            'intervals' => $intervals,
            'types' => $types,
        ];
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_P')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_note_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_P')]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_note_delete', methods: ['POST'])]
    #[IsGranted('ROLE_P')]
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/import/note', name: 'app_note_import', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): RedirectResponse
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '1024M');
        $excelFile = $request->files->get('excel_file');
        $matiereId = $request->request->get('matiere_id');

        if (!$excelFile) {
            $this->addFlash('error', 'Fichier manquant.');
            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        if (!$matiereId) {
            $this->addFlash('error', 'Aucune matière sélectionnée.');
            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        $matiere = $em->getRepository(Matiere::class)->find($matiereId);

        if (!$matiere) {
            $this->addFlash('error', 'Matière introuvable.');
            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        $newFilename = '';
        if ($excelFile) {
            if (!$excelFile->isValid()) {
            $this->addFlash('danger', 'Erreur lors de l\'upload du fichier');
            return $this->redirectToRoute('app_etudiant_index');
            }
            $originalFilename = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$excelFile->guessExtension();
            $uploadDir = $this->getParameter('uploads_directory');
            $filePath = $uploadDir . '/' . $newFilename;

            if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $this->addFlash('danger', 'Impossible de créer le répertoire d\'upload');
                return $this->redirectToRoute('app_etudiant_index');
            }
            }

            try {
            $excelFile->move(
                $uploadDir,
                $newFilename
            );
            } catch (FileException $e) {
            $this->addFlash('danger', 'Erreur lors de l\'enregistrement du fichier');
            return $this->redirectToRoute('import_rapport');
            }

            $spreadsheet = IOFactory::load($this->getParameter('uploads_directory') . '/' . $newFilename);
            $sheet = $spreadsheet->getActiveSheet();

            $studentRepository = $em->getRepository(Etudiant::class);
            $data = array();

            foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if(
                ($rowData[0] !== FileConstant::EXCEL_FILE_NUMERO->value and $row->getRowIndex() == 1) or
                ($rowData[1] !== FileConstant::EXCEL_FILE_MATRICULE->value and $row->getRowIndex() == 1) or
                ($rowData[2] !== FileConstant::EXCEL_FILE_NOM->value and $row->getRowIndex() == 1) or
                ($rowData[3] !== FileConstant::EXCEl_FILE_PRENOM->value and $row->getRowIndex() == 1) or
                ($rowData[4] !== FileConstant::EXCEL_FILE_NOTE_1->value and $row->getRowIndex() == 1) or
                ($rowData[5] !== FileConstant::EXCEL_FILE_NOTE_2->value and $row->getRowIndex() == 1) or
                ($rowData[6] !== FileConstant::EXCEL_FILE_NOTE_3->value and $row->getRowIndex() == 1) or
                ($rowData[7] !== FileConstant::EXCEL_FILE_MOYENNE->value and $row->getRowIndex() == 1)
            ){
                $this->addFlash('warning', 'Ce fichier excel est invalide, veuillez le remplacer');
                return $this->redirectToRoute('app_note_index');
            }

            if( $row->getRowIndex() == 1 )
                continue;

            $nb = $rowData[0];
            $matricule = trim($rowData[1]);
            $student = null;
            $note1 = $rowData[4];
            $note2 = $rowData[5];
            $note3 = $rowData[6];
            $notes = [$note1, $note2, $note3];

            if( $nb === null) {
                break;
            }

            if( ($student = $studentRepository->findOneBy(['matricule' => $matricule])) == null ){
                $this->addFlash('warning', 'Ce fichier contient un etudiant non inscrit !');
                return $this->redirectToRoute('app_note_index');
            };

            if( $em->getRepository(Note::class)->findOneBy(['student' => $student, 'matiere' => $matiere]) == null ){
                $this->addFlash('warning', 'Ce fichier contient un etudiant n\'ayant pas été enregistré!');
                return $this->redirectToRoute('app_note_index');
            }

            if( preg_match('/^\d{12}$/', $matricule) === 0){
                $this->addFlash('warning', "Ce fichier excel contient un matricule invalide. Matricule $matricule. Ligne : {$rowData[0]}");
                return $this->redirectToRoute('app_note_index');
            }

            foreach ($notes as $index => $note) {
                if ($note === null || $note === '') {
                $note = 0;
                }
                if (!is_numeric($note)) {
                $this->addFlash('warning', "La note " . ($index + 1) . " n'est pas un nombre valide. Ligne : {$nb}");
                return $this->redirectToRoute('app_note_index');
                }
                $noteFloat = (float) $note;
                if ($noteFloat < 0 || $noteFloat > 10) {
                $this->addFlash('warning', "La note " . ($index + 1) . " doit être comprise entre 0 et 10. Ligne : {$nb}");
                return $this->redirectToRoute('app_note_index');
                }
                $notes[$index] = $noteFloat;
            }

            $rowData = [$student, $notes[0], $notes[1], $notes[2]];
            $data[] = $rowData;
            }

            // Transaction start
            $connection = $em->getConnection();
            $connection->beginTransaction();
            try {
            foreach ($data as $dt){
                $note = $em->getRepository(Note::class)->findOneBy(['student' => $dt[0], 'matiere' => $matiere]);
                $note->setMatiere($matiere);
                $note->setStudent($dt[0]);
                $note->setNote1($dt[1]);
                $note->setNote2($dt[2]);
                $note->setNote3($dt[3]);
                $em->persist($note);
            }
            $em->flush();
            $connection->commit();
            $this->addFlash('success', 'Rapport importé avec succès');
            return $this->redirectToRoute('app_note_index');
            } catch (\Throwable $e) {
            $connection->rollBack();
            $this->addFlash('danger', 'Erreur lors de l\'import : ' . $e->getMessage());
            return $this->redirectToRoute('app_note_index');
            }
        }
        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }
}