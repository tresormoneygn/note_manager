<?php

namespace App\Form;

use App\Entity\Programme;
use App\Entity\Semestre;
use App\Entity\UniteEnseignement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniteEnseignementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'block w-full mt-1 p-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300',
                    'placeholder' => 'Nom',
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
            ])
            ->add('programme', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'block w-full mt-1 p-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300',
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'block w-full mt-1 p-2 border rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300',
                ],
                'label_attr' => ['class' => 'block text-sm font-medium text-gray-700'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UniteEnseignement::class,
        ]);
    }
}
