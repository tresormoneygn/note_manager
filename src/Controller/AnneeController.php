<?php

namespace App\Controller;

use App\Constant\FormConstant;
use App\Entity\Annee;
use App\Form\AnneeType;
use App\Helpers\AppHelper;
use App\Repository\AnneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/annee')]
final class AnneeController extends AbstractController
{
    #[Route(name: 'app_annee_index', methods: ['GET'])]
    public function index(AnneeRepository $anneeRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render('annee/index.html.twig', [
            'annees' => $anneeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_annee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annee = new Annee();
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verifier si l'année avec le même nom n'est pas dans la base de données
            if( $entityManager->getRepository(Annee::class)->findOneBy(['name' => $annee->getName()]) ){
                $this->addFlash(FormConstant::ALERT_ERROR->value, 'Cette année existe déjà.');
                return $this->redirectToRoute('app_annee_new');
            }
            $entityManager->persist($annee);
            $entityManager->flush();

            $this->addFlash(FormConstant::ALERT_SUCCESS->value, 'Ajout effectué avec succéss');
            return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annee/new.html.twig', [
            'annee' => $annee,
            'form' => $form,
            'button_label' => 'Ajouter',
            'button_add_class' => FormConstant::BUTTON_ADD_CLASS->value,
            'button_delete_class' => FormConstant::BUTTON_DELETE_CLASS->value,
            'button_modify_class' => FormConstant::BUTTON_MODIFY_CLASS->value,
        ]);
    }

    #[Route('/{id}', name: 'app_annee_show', methods: ['GET'])]
    public function show(Annee $annee): Response
    {
        return $this->render('annee/show.html.twig', [
            'annee' => $annee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annee/edit.html.twig', [
            'annee' => $annee,
            'form' => $form,
            'button_add_class' => FormConstant::BUTTON_ADD_CLASS->value,
            'button_delete_class' => FormConstant::BUTTON_DELETE_CLASS->value,
            'button_modify_class' => FormConstant::BUTTON_MODIFY_CLASS->value,
        ]);
    }

    #[Route('/{id}', name: 'app_annee_delete', methods: ['POST'])]
    public function delete(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($annee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
    }
}
