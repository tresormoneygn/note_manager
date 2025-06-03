<?php

namespace App\Controller;

use App\Constant\FileConstant;
use App\Constant\xtnsionConstant;
use App\Entity\Annee;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Entity\Programme;
use App\Entity\Rapport;
use App\Entity\UniteEnseignement;
use App\Form\ImportRapportType;
use App\Form\RapportType;
use App\Helpers\Constant;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/rapport')]
#[IsGranted('IS_AUTHENTICATED')]
class RapportController extends AbstractController
{
    #[Route('/import-rapport', name: 'import_rapport')]
    public function import(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response
    {
        try {
            ini_set('max_execution_time', 6000);
            ini_set('memory_limit', '1024M');

            $form = $this->createForm(ImportRapportType::class, options: [
                'user' => $this->getUser(),
                'annee' => $entityManager->getRepository(Annee::class)->findOneBy(['is_progress' => true]),
            ]);
            $form->handleRequest($request);
            $newFilename = '';

            if ($form->isSubmitted() && $form->isValid()) {
                $em->getConnection()->beginTransaction(); // 🚨 DÉBUT TRANSACTION

                $excelFile = $form->get('rapport')->getData();
                $matiere = $form->get('matiere')->getData();

                if ($excelFile && !$excelFile->isValid()) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload du fichier');
                    return $this->redirectToRoute('app_etudiant_index');
                }

                $originalFilename = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $excelFile->guessExtension();
                $uploadDir = $this->getParameter('uploads_directory');
                $filePath = $uploadDir . '/' . $newFilename;

                if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                    throw new \RuntimeException('Impossible de créer le répertoire d\'upload');
                }

                $excelFile->move($uploadDir, $newFilename);

                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();

                $studentRepository = $em->getRepository(Etudiant::class);
                $data = [];

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $rowData = [];

                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    if (
                        ($rowData[0] !== FileConstant::EXCEL_FILE_NUMERO->value && $row->getRowIndex() == 1) ||
                        ($rowData[1] !== FileConstant::EXCEL_FILE_MATRICULE->value && $row->getRowIndex() == 1) ||
                        ($rowData[2] !== FileConstant::EXCEL_FILE_NOM->value && $row->getRowIndex() == 1) ||
                        ($rowData[3] !== FileConstant::EXCEl_FILE_PRENOM->value && $row->getRowIndex() == 1) ||
                        ($rowData[8] !== FileConstant::EXCEL_FILE_SEXE->value && $row->getRowIndex() == 1)
                    ) {
                        throw new \Exception('Ce fichier excel est invalide, veuillez le remplacer');
                    }

                    if ($row->getRowIndex() == 1) continue;

                    $nb = $rowData[0];
                    $matricule = trim($rowData[1]);
                    $nom = $rowData[2];
                    $prenom = $rowData[3];
                    $sexe = $rowData[8];

                    if ($nb === null) break;

                    if ($studentRepository->findOneBy(['matricule' => $matricule])) {
                        throw new \Exception("Ce fichier contient un étudiant déjà inscrit ($matricule) ligne : $nb");
                    }

                    if (preg_match('/^\d{12}$/', $matricule) === 0) {
                        throw new \Exception("Matricule invalide : $matricule. Ligne : $nb");
                    }

                    if (!in_array($sexe, ['M', 'F'])) {
                        throw new \Exception("Sexe invalide : $sexe. Matricule : $matricule. Ligne : $nb");
                    }

                    $data[] = [$matricule, $nom, $prenom, $sexe];
                }

                foreach ($data as $dt) {
                    $std = new Etudiant();
                    $std->setMatricule($dt[0]);
                    $std->setNom($dt[1]);
                    $std->setPrenom($dt[2]);
                    $std->setSexe($dt[3]);
                    $em->persist($std);

                    $inscription = new Inscription();
                    $inscription->setProgramme($matiere->getUniteEnseignement()->getProgramme());
                    $inscription->setEtudiant($std);
                    $inscription->setAnnee($em->getRepository(Annee::class)->findOneBy(['is_progress' => true]));
                    $inscription->setClasse($em->getRepository(Classe::class)->findOneBy([
                        'id' => Constant::semestreToClassId($matiere->getUniteEnseignement()->getSemestre()->getName())
                    ]));
                    $em->persist($inscription);

                    $unite_enseignements = $em->getRepository(UniteEnseignement::class)->findBy([
                        'programme' => $matiere->getUniteEnseignement()->getProgramme()
                    ]);
                    foreach ($unite_enseignements as $unite_enseignement) {
                        $matieres = $em->getRepository(Matiere::class)->findBy(['uniteEnseignement' => $unite_enseignement]);
                        foreach ($matieres as $m) {
                            $note = new Note();
                            $note->setMatiere($m);
                            $note->setStudent($std);
                            $note->setNote1(0);
                            $note->setNote2(0);
                            $note->setNote3(0);
                            $em->persist($note);
                        }
                    }

                    $em->flush();
                }

                $rapport = new Rapport();
                $rapport->setFilename($newFilename);
                $rapport->setType(xtnsionConstant::EXCEL_FILE_XTNSION->value);
                $rapport->setCreatedAt(new \DateTimeImmutable());
                $rapport->setUpdatedAt(new \DateTimeImmutable());
                $rapport->setUser($security->getUser());

                $em->persist($rapport);
                $em->flush();
                $em->clear();

                $em->getConnection()->commit(); // ✅ VALIDER
                $this->addFlash('success', 'Rapport importé avec succès');
                return $this->redirectToRoute('app_etudiant_index');

            }

        } catch (\Throwable $e) {
            $em->getConnection()->rollBack(); // ❌ ANNULER
            $this->addFlash('danger', 'Une erreur est survenue : ' . $e->getMessage());
        }
        return $this->redirectToRoute('app_etudiant_index');
    }

    #[Route(name: 'app_rapport_index', methods: ['GET'])]
    public function index(RapportRepository $rapportRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'rapports' => $rapportRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rapport_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rapport = new Rapport();
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rapport);
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/new.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_show', methods: ['GET'])]
    public function show(Rapport $rapport): Response
    {
        return $this->render('rapport/show.html.twig', [
            'rapport' => $rapport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    }
}
