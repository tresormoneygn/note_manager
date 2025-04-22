<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Matricule de l\'étudiant'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Nom de famille'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Prénom(s)'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'email@exemple.com'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('numero', TelType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Numéro de téléphone'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('addresse', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Adresse complète'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100',
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('lieu_naissance', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Lieu de naissance'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700']
            ])
            ->add('pere', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Nom complet du père'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'required' => false
            ])
            ->add('mere', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Nom complet de la mère'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'required' => false
            ])
            ->add('nom_tuteur', TextType::class, [
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                    'placeholder' => 'Nom complet du tuteur'
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
            'attr' => ['class' => 'space-y-6']
        ]);
    }
}