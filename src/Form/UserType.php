<?php

namespace App\Form;

use App\Constant\FormConstant;
use App\Entity\Fonction;
use App\Entity\User;
use App\Helpers\Constant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse email']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide']),
                ],
                'attr' => [
                    'placeholder' => 'exemple@email.com',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => true,
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => Constant::roles(),
                'multiple' => true,
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                ],
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'autocomplete' => 'new-password',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                ]
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer un prénom'])],
                'attr' => [
                    'placeholder' => 'Jean',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => true,
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer un nom'])],
                'attr' => [
                    'placeholder' => 'Dupont',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => true,
                ]
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => false,
                ],
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer un matricule'])],
                'attr' => [
                    'placeholder' => '2023A001',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => false,
                ]
            ])
            ->add('numero', TelType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer un numéro'])],
                'attr' => [
                    'placeholder' => '+2250700000000',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => false,
                ]
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [new NotBlank(['message' => 'Veuillez entrer une adresse'])],
                'attr' => [
                    'placeholder' => 'Cocody, Abidjan',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => false,
                ]
            ]);
            /*->add('fonctions', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'name', // remplace 'id' si tu as un champ `nom` dans l'entité
                'multiple' => true,
                'label' => 'Fonctions',
                'attr' => [
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,

                ],
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
