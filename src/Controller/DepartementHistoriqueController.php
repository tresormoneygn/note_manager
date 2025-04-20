<?php

namespace App\Controller;

use App\Entity\DepartementHistorique;
use App\Entity\Departement;
use App\Form\DepartementHistoriqueType;
use App\Repository\DepartementHistoriqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;

#[Route('/departementhistorique')]
final class DepartementHistoriqueController extends AbstractController
{
    #[Route('/',name: 'app_departement_historique_index', methods: ['GET'])]
    public function index(DepartementHistoriqueRepository $departementHistoriqueRepository): Response
    {
        return $this->render('departement_historique/index.html.twig', [
            'departement_historiques' => $departementHistoriqueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_departement_historique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departementHistorique = new DepartementHistorique();
        $form = $this->createForm(DepartementHistoriqueType::class, $departementHistorique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Formatage du nom
            //$departementHistorique->setName(ucwords(strtolower($departementHistorique->getName())));

            // Vérification doublon pour même annee, même département et même nom (sans casse)
            $exist = $entityManager->createQueryBuilder()
                ->select('h')
                ->from(DepartementHistorique::class, 'h')
                ->join('h.departement', 'd')
                ->where('h.annee = :annee')
                ->andWhere('h.departement = :departement')
                ->andWhere('LOWER(d.name) = :name')
                ->setParameters(new ArrayCollection([
                    new Parameter('annee', $departementHistorique->getAnnee()),
                    new Parameter('departement', $departementHistorique->getDepartement()),
                    new Parameter('name', strtolower($departementHistorique->getDepartement()->getName())),
                ]))
                ->getQuery()
                ->getOneOrNullResult();
            

            if ($exist) {
                $this->addFlash('error', 'Ce département historique existe déjà pour cette année et ce département.');
            } else {
                $now = new \DateTimeImmutable();
                $departementHistorique->setCreatedAt($now);
                $departementHistorique->setUpdatedAt($now);

                $entityManager->persist($departementHistorique);
                $entityManager->flush();

                $this->addFlash('success', 'Département historique enregistré avec succès.');
                return $this->redirectToRoute('app_departement_historique_index');
            }
        }

        return $this->render('departement_historique/new.html.twig', [
            'departement_historique' => $departementHistorique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_historique_show', methods: ['GET'])]
    public function show(DepartementHistorique $departementHistorique): Response
    {
        return $this->render('departement_historique/show.html.twig', [
            'departement_historique' => $departementHistorique,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_departement_historique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DepartementHistorique $departementHistorique, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementHistoriqueType::class, $departementHistorique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_departement_historique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('departement_historique/edit.html.twig', [
            'departement_historique' => $departementHistorique,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_historique_delete', methods: ['POST'])]
    public function delete(Request $request, DepartementHistorique $departementHistorique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departementHistorique->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($departementHistorique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departement_historique_index', [], Response::HTTP_SEE_OTHER);
    }
}
