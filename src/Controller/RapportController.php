<?php

namespace App\Controller;

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

#[Route('/rapport')]
final class RapportController extends AbstractController
{
    #[Route('/import-rapport', name: 'import_rapport')]
    public function import(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security): Response
    {
        $form = $this->createForm(ImportRapportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $excelFile = $form->get('rapport')->getData();

            if ($excelFile) {
                $originalFilename = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$excelFile->guessExtension();

                try {
                    $excelFile->move(
                        $this->getParameter('uploads_directory'), // définie dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'enregistrement du fichier');
                    return $this->redirectToRoute('import_rapport');
                }

                // Lecture du fichier Excel
                $spreadsheet = IOFactory::load($this->getParameter('uploads_directory') . '/' . $newFilename);
                $sheet = $spreadsheet->getActiveSheet();

                foreach ($sheet->getRowIterator(2) as $row) { // ligne 1 = entêtes
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    dd($rowData);

                    // Traiter chaque ligne ici
                    // Exemple : créer un user à partir des données
                    // $rowData = [email, password, first_name, ...]
                }

                // Sauvegarder un Rapport
//                $rapport = new Rapport();
//                $rapport->setFilename($newFilename);
//                $rapport->setType($excelFile->getMimeType());
//                $rapport->setCreatedAt(new \DateTimeImmutable());
//                $rapport->setUpdatedAt(new \DateTimeImmutable());
//                $rapport->setUser($security->getUser());
//
//                $em->persist($rapport);
//                $em->flush();

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
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    }
}
