<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionWithDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', ChoiceType::class, [
                'label' => 'Datum',
                'choices' => [
                    'Montag, 20. April 2020' => '2020-04-20',
                    'Dienstag, 21. April 2020' => '2020-04-21',
                    'Mittwoch, 22. April 2020' => '2020-04-22',
                    'Donnerstag, 23. April 2020' => '2020-04-23',
                    'Freitag, 24. April 2020' => '2020-04-24',
                    'Samstag, 25. April 2020' => '2020-04-25',
                    'Sonntag, 26. April 2020' => '2020-04-26',
                    'Montag, 27. April 2020' => '2020-04-27',
                ]
            ]);

        $builder->get('date')
            ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d'));

        $builder
            ->add('start', TimeType::class, [
                'label' => 'Beginn',
                'widget' => 'single_text',
                'html5' => false,
            ])
            ->add('stop', TimeType::class, [
                'label' => 'Ende',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'Titel',
                'attr' => [
                    'maxlength' => 30,
                ]
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Kurzbeschreibung',
                'required' => false,
                'attr' => [
                    'maxlength' => 250,
                ]
            ])
            ->add('longDescription', TextareaType::class, [
                'label' => 'Langbeschreibung',
                'required' => false,
            ])
            ->add('locationName', TextType::class, [
                'label' => 'Veranstaltungsort',
            ])
            ->add('link', TextType::class, [
                'label' => 'Link',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SessionWithDetail::class,
        ]);
    }
}
