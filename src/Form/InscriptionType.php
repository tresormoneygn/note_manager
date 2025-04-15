<?php

namespace App\Form;

use App\Entity\Annee;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Entity\Programme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'id',
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'id',
            ])
            ->add('annee', EntityType::class, [
                'class' => Annee::class,
                'choice_label' => 'id',
            ])
            ->add('programme', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
