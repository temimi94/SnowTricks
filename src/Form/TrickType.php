<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Form\ImageType;
use App\Entity\Tricks;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])

            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class'   => Categorie::class,
                    'choice_label'    => 'title',
                    'multiple' => false,
                    'label' => 'Groupe',
                ]
            )

            ->add('TriksImage', FileType::class, [
                'multiple' => true,
                'label' => 'Ajouter une photo',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'attr' => ['class' => 'form-control'],
                'mapped' => false
            ])
            ->add('videos', CollectionType::class,  [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
               
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary shadow mt-4 mb-4 col-md-2 '
                ],
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
