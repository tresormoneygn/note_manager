<?php

namespace App\Form;

use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FiltreNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricule', TextType::class, [
                'required' => false,
                'label' => 'Matricule',
                'attr' => ['placeholder' => 'Entrer le matricule']
            ])
            
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrer le nom']
            ])
            ->add('annee', TextType::class, [
                'required' => false,
                'label' => 'Année',
                'attr' => ['placeholder' => 'Entrer l\'année']
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => '-- Choisir une matière --',
                'label' => 'Matière'
            ])
            ->add('filtrer', SubmitType::class, [
                'label' => 'Rechercher'
            ]);
    }
}
