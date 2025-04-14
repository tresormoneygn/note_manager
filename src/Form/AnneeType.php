<?php

namespace App\Form;

use App\Entity\Annee;
use App\Constant\FormConstant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AnneeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', type: TextType::class, options: [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez renseigner l\'année']),
                    new Regex(pattern: [
                        'pattern' => '/^\d{4}-\d{4}$/',
                        'message' => 'Le format doit être "2022-2023".',
                    ])
                ],
                'attr' => [
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'placeholder' => '2022-2023',
                    'disabled' => false,
                    'autofocus' => true,
                    'maxlength' => 9,
                    'minlength' => 9,
                    'id' => 'name'
                ],
            ])
            ->add('is_progress', CheckboxType::class, [
                'label' => 'En cours ?',
                'required' => false,
                'attr' => [
                    'class' => FormConstant::CHECK_BOX_TYPE_CLASS->value,
                    'id' => 'is_progress',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annee::class,
        ]);
    }
}
