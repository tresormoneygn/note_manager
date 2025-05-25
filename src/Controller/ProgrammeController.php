<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Form\ProgrammeType;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/programme')]
#[IsGranted('IS_AUTHENTICATED')]
class ProgrammeController extends AbstractController
{
    #[Route('/', name: 'app_programme_index', methods: ['GET'])]
    public function index(ProgrammeRepository $programmeRepository): Response
    {
        return $this->render('programme/index.html.twig', [
            'programmes' => $programmeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_programme_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DG')]
    public function new(Request $request, EntityManagerInterface $entityManager, ProgrammeRepository $programmeRepository): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Mise en forme automatique
            $programme->setLabel(strtoupper($programme->getLabel()));
            $programme->setName(ucwords(strtolower($programme->getName())));
            $now = new \DateTimeImmutable();
            $programme->setCreatedAt($now);
            $programme->setUpdatedAt($now);

            // 2. Vérifier s’il existe déjà un doublon
            $exist = $programmeRepository->createQueryBuilder('p')
                ->where('p.annee = :annee')
                ->andWhere('p.departement = :departement')
                ->andWhere('p.label = :label OR p.name = :name')
                ->setParameters(new ArrayCollection([
                    new Parameter('annee', $programme->getAnnee()),
                    new Parameter('departement', $programme->getDepartement()),
                    new Parameter('label', $programme->getLabel()),
                    new Parameter('name', $programme->getName()),
                ]))
                ->getQuery()
                ->getOneOrNullResult();

                if ($exist) {
                    $this->addFlash('error', 'Un programme avec ce label ou ce nom existe déjà pour cette année et ce département.');
                } else {
                    $entityManager->persist($programme);
                    $entityManager->flush();
            
                    $this->addFlash('success', 'Programme ajouté avec succès.');
                    return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
                }
        }

        return $this->render('programme/new.html.twig', [
            'programme' => $programme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_show', methods: ['GET'])]
    public function show(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_programme_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_DG')]
    public function edit(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('programme/edit.html.twig', [
            'programme' => $programme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_programme_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DG')]
    public function delete(Request $request, Programme $programme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($programme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_programme_index', [], Response::HTTP_SEE_OTHER);
    }
}
