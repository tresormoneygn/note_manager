<?php
namespace App\Form;

use App\Constant\FormConstant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportRapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rapport', FileType::class, [
                'label' => 'Fichier Excel',
                'mapped' => false, // car ce champ ne fait pas partie d'une entité
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                            'application/vnd.ms-excel', // .xls
                        ],
                        'mimeTypesMessage' => 'Veuillez importer un fichier Excel valide',
                    ])
                ],
                'attr' => [
                    'class' => FormConstant::SELECT_TYPE_CLASS->value,
                ]
            ]);
    }
}
