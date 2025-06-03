<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/matiere')]
#[IsGranted('IS_AUTHENTICATED')]
final class MatiereController extends AbstractController
{
    #[Route(name: 'app_matiere_index', methods: ['GET'])]
    public function index(MatiereRepository $matiereRepository): Response
    {
        // Rajouter la pagination si nécessaire
        
        return $this->render('matiere/index.html.twig', [
            'matieres' => $matiereRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_matiere_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DP')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uniteEnseignement = $matiere->getUniteEnseignement();
            if ($uniteEnseignement) {
                // Vérifier le coefficient de la matière courante
                if ($matiere->getCoefficient() > 6) {
                    $this->addFlash('error', 'Le coefficient de la matière ne doit pas dépasser 6.');
                    return $this->redirectToRoute('app_matiere_new', [], Response::HTTP_SEE_OTHER);
                }

                // Calculer la somme des coefficients
                $totalCoefficients = 0;
                $matieres = $uniteEnseignement->getMatieres();

                foreach ($matieres as $m) {
                    $totalCoefficients += $m->getCoefficient();
                }

                // Ajouter le coefficient de la nouvelle matière
                $totalCoefficients += $matiere->getCoefficient();

                // Vérifier le total
                if ($totalCoefficients > 6) {
                    $this->addFlash('error', sprintf(
                        'La somme des coefficients des matières (%s) dépasse 6 pour cette unité d\'enseignement. ' .
                        'Coefficient maximum autorisé: 6.',
                        $totalCoefficients
                    ));
                    return $this->redirectToRoute('app_matiere_new', [], Response::HTTP_SEE_OTHER);
                }
            }

            // Le nom renvoi toujours null mettre ce hack ici momentannement
            $entityManager->persist($matiere);
            $entityManager->flush();

            return $this->redirectToRoute('app_matiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matiere/new.html.twig', [
            'matiere' => $matiere,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_matiere_show', methods: ['GET'])]
    public function show(Matiere $matiere): Response
    {
        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_matiere_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DP')]
    public function edit(Request $request, Matiere $matiere, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_matiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matiere/edit.html.twig', [
            'matiere' => $matiere,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matiere_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DP')]
    public function delete(Request $request, Matiere $matiere, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matiere->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($matiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_matiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
