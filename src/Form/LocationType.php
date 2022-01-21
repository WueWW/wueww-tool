<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Location-/Gebäudename (ggf. Firmenname)',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'cancel-return',
                ],
            ])
            ->add('streetNo', TextType::class, [
                'label' => 'Straße und Hausnummer',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'cancel-return',
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'PLZ',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'cancel-return',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ort',
                'empty_data' => '',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'cancel-return',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
