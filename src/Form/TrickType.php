<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Tricks;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                ]
            ])
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $t) {
                        return $t->createQueryBuilder('c');
                    },

                    'label' => 'Groupe',
                    'label_attr' => [
                        'class' => 'form-label mt-4'
                    ],

                    'choice_label' => 'name',
                    'multiple' => true
                ]
            )
            ->add('image')
            ->add('video')
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Créer une figure'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
