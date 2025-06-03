<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Programme;
use App\Entity\Fonction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fonctions', EntityType::class, [
                'class' => Fonction::class,
                'choice_label' => 'name',
                'label' => 'Fonction(s)',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'flex flex-col space-y-3' // Disposition en colonne avec espacement vertical
                ],
                'row_attr' => [
                    'class' => 'mb-6' // Marge en bas du groupe
                ],
                'label_attr' => [
                    'class' => 'block mb-3 text-sm font-medium text-gray-700' // Style du label principal
                ],
                'help_attr' => [
                    'class' => 'mt-1 text-sm text-gray-500' // Style du texte d'aide
                ],
                'choice_attr' => function() {
                    return [
                        'class' => 'mr-2 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500' // Style des checkboxes
                    ];
                },
                'help' => 'Sélectionnez une ou plusieurs fonctions pour cet utilisateur'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                ],
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre date de naissance']),
                ],
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule / ID',
                'required' => false,
                'help' => 'Identifiant unique pour le personnel (facultatif)',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone']),
                ],
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les <a href="#" class="text-blue-600 hover:text-blue-800 hover:underline">conditions d\'utilisation</a>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'ml-2 block text-sm text-gray-700 cursor-pointer'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisation.',
                    ]),
                ],
                'attr' => [
                    'class' => 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer'
                ],
                'row_attr' => [
                    'class' => 'flex items-start'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
            
        // Aucune validation spécifique nécessaire pour le moment
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
