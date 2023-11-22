<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => ['autocomplete' => 'email'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer une adresse Email',
                ]),
            ],
        ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary shadow mt-4 mb-4 col-md-6 '
                ],
                'label' => 'Réinitialisation'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
