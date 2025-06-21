<?php
namespace App\Form;

use App\Constant\FormConstant;
use App\Entity\Annee;
use App\Entity\Matiere;
use App\Entity\Programme;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportRapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var UserInterface $user */
        $user = $options['user'];

        /** @var Annee $annee */
        $annee = $options['annee'];

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
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => function(Matiere $matiere) {
                    return $matiere->getName() . ' (' . $matiere->getUniteEnseignement()->getProgramme()->getName(). ')';
                },
                'required' => true,
                'placeholder' => '-- Choisir une matière --',
                'label' => 'Matière',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('m')
                        ->where('m.user = :user')
                        ->setParameter('user', $user);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'user' => null,
            'annee' => null,
        ]);
    }
}
