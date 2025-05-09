<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note_1', NumberType::class, [
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'min' => 0,
                    'max' => 10,
                ],
                'html5' => true,
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('note_2', NumberType::class, [
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'min' => 0,
                    'max' => 10,
                ],
                'html5' => true,
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('note_3', NumberType::class, [
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'min' => 0,
                    'max' => 10,
                ],
                'html5' => true,
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => function(Matiere $matiere) {
                    return $matiere->getName() . ' (' . $matiere->getCoefficient() . ')'; // Affiche nom et prénom
                }
                , // Modifié pour afficher le nom plutôt que l'id
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('student', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function(Etudiant $etudiant) {
                    return $etudiant->getNom() . ' ' . $etudiant->getPrenom() . ' ' . $etudiant->getMatricule(); // Affiche nom et prénom
                },
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}