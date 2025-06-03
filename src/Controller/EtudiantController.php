<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Form\EtudiantType;
use App\Form\ImportRapportType;
use App\Form\FiltreEtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/etudiant')]
#[IsGranted('IS_AUTHENTICATED')]
class EtudiantController extends AbstractController
{
    #[Route(name: 'app_etudiant_index', methods: ['GET'])]
    public function index(
        EtudiantRepository $etudiantRepository,
        Request $request,
        PaginatorInterface $paginator,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $importForm = $this->createForm(ImportRapportType::class, options: [
            'user' => $this->getUser(),
            'annee' => $entityManager->getRepository(Annee::class)->findOneBy(['is_progress'=> true]),
        ]);

        $user = $security->getUser();
        $filtreForm = $this->createForm(FiltreEtudiantType::class, null, [
            'method' => 'GET',
            'user_matieres' => $user->getMatieres()
        ]);
        $filtreForm->handleRequest($request);

        // Construire la requête en fonction des critères
        $queryBuilder = $etudiantRepository->createQueryBuilder('e');

        if ($filtreForm->isSubmitted() && $filtreForm->isValid()) {
            $data = $filtreForm->getData();
            $matricule = $data['matricule'] ?? null;
            $nom = $data['nom'] ?? null;
            $matiere = $data['matiere'] ?? null;

            $queryBuilder = $etudiantRepository->filtrerEtudiant($matricule, $nom, $matiere);
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
            'form' => $importForm->createView(),
            'filtre_form' => $filtreForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DG') or is_granted('ROLE_DGA_E')")]
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
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }
}
