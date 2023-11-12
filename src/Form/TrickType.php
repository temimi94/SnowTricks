<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Tricks;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control ',
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
                ]
            ])
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'placeholder' => 'choisir le groupe',
                    'query_builder' => fn (CategoryRepository $t) =>
                    $t->createQueryBuilder('c'),

                    'label' => 'Groupe',
                    'label_attr' => [
                        'class' => 'mr-sm-2 mt-4'
                    ],


                    'multiple' => true
                ]
            )
            ->add(
                'image',
                UrlType::class,
                [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Image',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ]
                ]

            )
            ->add('video', UrlType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Vidéo',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary shadow mt-4 col-md-4'
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
