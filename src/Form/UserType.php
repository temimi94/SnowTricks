<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlenght' => '2',
                        'maxlenght' => '50',
                    ],
                    'required' => false,
                    'label' => "Nom d'utilisateur",
                    'label_attr' => [
                        'class' => 'form-label  mt-4'
                    ],
                    'constraints' => [
                        new Assert\Length(['min' => 2, 'max' => 50])
                    ]
                ]
            )

            ->add('reset_token', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])

            ->add('imageFile', VichImageType::class, [
                'label' => "Avatar",
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary shadow mt-4 mb-4 col-md-4 '
                ],
                'label' => 'Modifier mon compte'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
