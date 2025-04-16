<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Programme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('userType', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Type d\'utilisateur',
                'choices' => [
                    'Professeur' => 'teacher',
                    'Personnel administratif' => 'admin'
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => ['class' => 'mb-3']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Veuillez entrer un email valide']),
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                ],
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre date de naissance']),
                ],
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule / ID',
                'required' => false,
                'help' => 'Identifiant unique pour le personnel (facultatif)',
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone']),
                ],
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse']),
                ],
            ])
            ->add('programme', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez votre programme',
                'required' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
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
