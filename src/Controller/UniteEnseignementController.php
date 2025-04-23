<?php

namespace App\Controller;

use App\Entity\UniteEnseignement;
use App\Form\UniteEnseignementType;
use App\Repository\UniteEnseignementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/unite/enseignement')]
#[IsGranted('IS_AUTHENTICATED')]
final class UniteEnseignementController extends AbstractController
{
    #[Route(name: 'app_unite_enseignement_index', methods: ['GET'])]
    public function index(UniteEnseignementRepository $uniteEnseignementRepository): Response
    {
        return $this->render('unite_enseignement/index.html.twig', [
            'unite_enseignements' => $uniteEnseignementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_unite_enseignement_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uniteEnseignement = new UniteEnseignement();
        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($uniteEnseignement);
            $entityManager->flush();

            return $this->redirectToRoute('app_unite_enseignement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('unite_enseignement/new.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_unite_enseignement_show', methods: ['GET'])]
    public function show(UniteEnseignement $uniteEnseignement): Response
    {
        return $this->render('unite_enseignement/show.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_unite_enseignement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, UniteEnseignement $uniteEnseignement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_unite_enseignement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('unite_enseignement/edit.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_unite_enseignement_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, UniteEnseignement $uniteEnseignement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uniteEnseignement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($uniteEnseignement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_unite_enseignement_index', [], Response::HTTP_SEE_OTHER);
    }
}
