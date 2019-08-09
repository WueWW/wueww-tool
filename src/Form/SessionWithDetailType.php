<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('date', DateType::class, [
                'label' => 'Datum',
                'widget' => 'single_text',
                'html5' => false,
            ])
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
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Kurzbeschreibung',
                'required' => false,
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
