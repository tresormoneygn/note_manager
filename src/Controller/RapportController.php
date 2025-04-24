<?php

namespace App\Controller;

use App\Constant\FileConstant;
use App\Constant\xtnsionConstant;
use App\Entity\Etudiant;
use App\Entity\Rapport;
use App\Form\ImportRapportType;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
    #[IsGranted('ROLE_ADMIN')]
    public function import(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security): Response
    {
        $form = $this->createForm(ImportRapportType::class);
        $form->handleRequest($request);
        $newFilename = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $excelFile = $form->get('rapport')->getData();
            $newFilename = '';
            if ($excelFile) {
                // Vérification que le fichier est bien uploadé
                if (!$excelFile->isValid()) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload du fichier');
                    return $this->redirectToRoute('import_rapport');
                }
                $originalFilename = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$excelFile->guessExtension();
                $uploadDir = $this->getParameter('uploads_directory');
                $filePath = $uploadDir . '/' . $newFilename;

                // Création du répertoire s'il n'existe pas
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                        $this->addFlash('danger', 'Impossible de créer le répertoire d\'upload');
                        return $this->redirectToRoute('import_rapport');
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

                // Lecture du fichier Excel
                $spreadsheet = IOFactory::load($this->getParameter('uploads_directory') . '/' . $newFilename);
                $sheet = $spreadsheet->getActiveSheet();

                $studentRepository = $em->getRepository(Etudiant::class);
                $data = array();
                foreach ($sheet->getRowIterator() as $row) { // ligne 1 = entêtes
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    // Vérification si l'entête correspond bien a ce qui est attendu
                    if(
                        ($rowData[0] !== FileConstant::EXCEL_FILE_NUMERO->value and $row->getRowIndex() == 1) or
                        ($rowData[1] !== FileConstant::EXCEL_FILE_MATRICULE->value and $row->getRowIndex() == 1) or
                        ($rowData[2] !== FileConstant::EXCEL_FILE_NOM->value and $row->getRowIndex() == 1) or
                        ($rowData[3] !== FileConstant::EXCEl_FILE_PRENOM->value and $row->getRowIndex() == 1)
                    ){
                        $this->addFlash('warning', 'Ce fichier excel est invalide, veuillez le remplacer');
                        return $this->redirectToRoute('import_rapport');
                    }


                    // Verifier si on est plus a la premiere ligne
                    if( $row->getRowIndex() == 1 )
                        continue;

                    $nb = $rowData[0];
                    $matricule = trim($rowData[1]);
                    $nom = $rowData[2];
                    $prenom = $rowData[3];

                    if( $nb === null) {
                        break;
                    }

                    if( $studentRepository->findOneBy(['matricule' => $matricule]) != null ){
                        $this->addFlash('warning', 'Ce fichier contient un etudiant déja inscrit !');
                        return $this->redirectToRoute('import_rapport');
                    };


                    if( preg_match('/^\d{12}$/', $matricule) === 0){
                        $this->addFlash('warning', "Ce fichier excel contient un matricule invalide. Matricule $matricule. Ligne : {$rowData[0]}");
                        return $this->redirectToRoute('import_rapport');
                    }

                    $rowData = [$matricule, $nom, $prenom];
                    $data[] = $rowData;
                }

                foreach ($data as $dt){
                    $std = new Etudiant();
                    $std->setMatricule($dt[0]);
                    $std->setNom($dt[1]);
                    $std->setPrenom($dt[2]);

                    $em->persist($std);
                    $em->flush();
                }

                // Sauvegarder un Rapport
                $rapport = new Rapport();
                $rapport->setFilename($newFilename);
                $rapport->setType(xtnsionConstant::EXCEL_FILE_XTNSION->value);
                $rapport->setCreatedAt(new \DateTimeImmutable());
                $rapport->setUpdatedAt(new \DateTimeImmutable());
                $rapport->setUser($security->getUser());

                $em->persist($rapport);
                $em->flush();

                $this->addFlash('success', 'Rapport importé avec succès');
                return $this->redirectToRoute('import_rapport');
            }
        }

        return $this->render('rapport/import.html.twig', [
            'form' => $form->createView(),
        ]);
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
