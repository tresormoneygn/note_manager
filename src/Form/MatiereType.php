<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\UniteEnseignement;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200',
                    'placeholder' => 'Entrez le nom (Ex : Java)'
                ],
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-700'
                ],
                'label'=> 'Nom de la matière'
            ])
            ->add('coefficient', null, [
                'attr' => [
                    'class' => 'w-full px-4 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500',
                    'placeholder' => 'Coefficient',
                    'min' => 1,
                    'step' => '1',
                    'max' => 6,
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ]
            ])
            ->add('uniteEnseignement', EntityType::class, [
                'class' => UniteEnseignement::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'w-full px-4 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500'
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ],
                'placeholder' => 'Sélectionnez une unité d\'enseignement'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Professeur',
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->findByFonctionQueryBuilder('P');
                },
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500',
                    'required' => true
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-medium text-gray-700'
                ],
                'placeholder' => 'Sélectionnez le professeur'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
