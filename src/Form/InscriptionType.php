<?php

namespace App\Form;

use App\Entity\Annee;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Entity\Programme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'label' => 'Classe',
                'attr' => [
                    'class' => 'block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700',
                ],
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'matricule',
                'label' => 'Etudiant',
                'attr' => [
                    'class' => 'block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700',
                ],
            ])
            ->add('annee', EntityType::class, [
                'class' => Annee::class,
                'choice_label' => 'name',
                'label' => 'Année',
                'attr' => [
                    'class' => 'block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700',
                ],
            ])
            ->add('programme', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'name',
                'label' => 'Programme',
                'attr' => [
                    'class' => 'block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500',
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
