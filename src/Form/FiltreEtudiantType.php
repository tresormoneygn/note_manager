<?php

namespace App\Form;

use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreEtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userMatieres = $options['user_matieres'] ?? [];
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
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choices' => $userMatieres,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => '-- Choisir une matière --',
                'label' => 'Matière'
            ])
            ->add('filtrer', SubmitType::class, [
                'label' => 'Rechercher'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user_matieres' => [],
        ]);
    }
}