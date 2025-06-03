<?php

namespace App\Form;

use App\Entity\Annee;
use App\Entity\DepartementHistorique;
use App\Entity\Programme;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200',
                    'placeholder' => 'Entrez le nom (Ex : Développement Logiciel)'
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700'
                ],
                'label'=> 'Nom du programme'
            ])
            ->add('label', null, [
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200',
                    'placeholder' => 'Entrez le label (Ex : DL)'
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700'
                ],
                'label'=> 'Label du Programme'
            ])
            ->add('departement', EntityType::class, [
                'class' => DepartementHistorique::class,
                'label' => 'Département',
                'choice_label' => 'departement.name', // Chemin vers la propriété name
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200'
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700'
                ],
                'placeholder' => 'Sélectionnez un département'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Directeur de Programme',
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->findByFonctionQueryBuilder('DP');
                },
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500',
                    'required' => true
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
