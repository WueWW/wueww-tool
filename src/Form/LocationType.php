<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Location-/Gebäudename (ggf. Firmenname)',
            ])
            ->add('streetNo', TextType::class, [
                'label' => 'Straße und Hausnummer',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'PLZ',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ort',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
