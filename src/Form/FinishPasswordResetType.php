<?php


namespace App\Form;


use App\DTO\FinishPasswordReset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinishPasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Die erfassten Passwörter stimmen nicht überein',
                'first_options' => [
                    'label' => 'Neues Passwort:',
                ],
                'second_options' => [
                    'label' => 'Neues Passwort (Wiederholung):',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FinishPasswordReset::class,
        ]);
    }
}