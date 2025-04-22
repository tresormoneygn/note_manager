<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Form\ImportRapportType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etudiant')]
final class EtudiantController extends AbstractController
{
    #[Route(name: 'app_etudiant_index', methods: ['GET'])]
    public function index(
        EtudiantRepository $etudiantRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $form = $this->createForm(ImportRapportType::class);

        // Récupérer les critères de recherche
        $matricule = $request->query->get('matricule');
        $nom = $request->query->get('nom');
        $prenom = $request->query->get('prenom');

        // Construire la requête en fonction des critères
        $queryBuilder = $etudiantRepository->createQueryBuilder('e');

        if ($matricule) {
            $queryBuilder->andWhere('e.matricule LIKE :matricule')
                ->setParameter('matricule', '%'.$matricule.'%');
        }

        if ($nom) {
            $queryBuilder->andWhere('e.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($prenom) {
            $queryBuilder->andWhere('e.prenom LIKE :prenom')
                ->setParameter('prenom', '%'.$prenom.'%');
        }

        // Paginer les résultats
        $etudiants = $paginator->paginate(
            $queryBuilder->getQuery(), // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page, 1 par défaut
            10, // Nombre d'éléments par page
            [
                'defaultSortFieldName' => 'e.id',
                'defaultSortDirection' => 'asc',
            ]
        );

        return $this->render('etudiant/index.html.twig', [
            'etudiants' => $etudiants,
            'active_page' => 'etudiant',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
            'active_page' => 'etudiant'
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('etudiant/show.html.twig', [
            'etudiant' => $etudiant,
            'active_page' => 'etudiant',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
            'active_page' => 'etudiant'
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }
}
