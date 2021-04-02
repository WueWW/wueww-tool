<?php

namespace App\Form;

use App\DTO\UserRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-Mail-Adresse:',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Die erfassten Passwörter stimmen nicht überein',
                'first_options' => [
                    'label' => 'Neues Passwort (mind. acht Zeichen):',
                    'attr' => [
                        'minlength' => 8,
                    ],
                ],
                'second_options' => [
                    'label' => 'Passwort (Wiederholung):',
                    'attr' => [
                        'minlength' => 8,
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistration::class,
        ]);
    }
}
