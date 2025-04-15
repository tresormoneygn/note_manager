<?php

namespace App\Form;

use App\Constant\FormConstant;
use App\Entity\Fonction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FonctionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', type: TextType::class, options: [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Professeur',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => true,
                ]
            ])
            ->add('label', type: TextType::class, options: [
                'label' => 'Label',
                'attr' => [
                    'placeholder' => 'DGA/R',
                    'autocomplete' => 'off',
                    'class' => FormConstant::TEXT_TYPE_CLASS->value,
                    'required' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fonction::class,
        ]);
    }
}
