<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Form\FiltreNoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/note')]
final class NoteController extends AbstractController
{
    #[Route(name: 'app_note_index', methods: ['GET'])]
    public function index(Request $request, NoteRepository $noteRepository): Response
    {
        $form = $this->createForm(FiltreNoteType::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $notes = [];
        $noteData = []; // Pour stocker les données du graphique

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $matricule = $data['matricule'] ?? null;
            $nom = $data['nom'] ?? null;
            $annee = $data['annee'] ?? null;

            $notes = $noteRepository->filtrerNote($matricule, $nom, $annee);
            // Calculer les statistiques des notes
            $noteData = $this->calculateNoteStatistics($notes);
        } else {
            $notes = $noteRepository->findAll();
        }

        return $this->render('note/index.html.twig', [
            'form' => $form->createView(),
            'notes' => $notes,
            'noteData' => $noteData, // Passer les données au template
        ]);

        // return $this->render('note/index.html.twig', [
        //     'form' => $form->createView(),
        //     'notes' => $noteRepository->findAll(),
        // ]);



        
    }


    private function calculateNoteStatistics($notes)
    {
        // Exemple de calcul de répartition des notes
        $noteData = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

        foreach ($notes as $note) {
            if ($note->getValue() >= 9) {
                $noteData['A']++;
            } elseif ($note->getValue() >= 8) {
                $noteData['B']++;
            } elseif ($note->getValue() >= 7) {
                $noteData['C']++;
            } elseif ($note->getValue() >= 6) {
                $noteData['D']++;
            } else {
                $noteData['E']++;
            }
        }

        return $noteData;
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
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
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }



   
}
