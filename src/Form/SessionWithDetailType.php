<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionWithDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, ['widget' => 'single_text', 'html5' => false])
            ->add('stop', DateTimeType::class, ['widget' => 'single_text', 'html5' => false, 'required' => false])
            ->add('cancelled', CheckboxType::class, ['required' => false])
            ->add('title', TextType::class)
            ->add('shortDescription', TextareaType::class, ['required' => false])
            ->add('longDescription', TextareaType::class, ['required' => false])
            ->add('locationName', TextType::class)
            ->add('link', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SessionWithDetail::class,
        ]);
    }
}
