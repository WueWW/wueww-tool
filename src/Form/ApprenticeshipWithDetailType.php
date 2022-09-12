<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ApprenticeshipWithDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', LocationType::class, [
                'label' => 'Veranstaltungsort',
            ])
            ->add('locationLat', HiddenType::class)
            ->add('locationLng', HiddenType::class)
            ->add('jobs', JobChoiceType::class)
            ->add('jobsUrl', TextType::class, [
                'label' => 'Link zu Stellenangeboten oder Karriere-Seite',
                'attr' => [
                    'maxlength' => 255,
                ],
            ]);
    }
}
