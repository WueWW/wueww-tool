<?php

namespace App\Form;

use App\Entity\OrganizationDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('contactName', TextType::class, [
                'label' => 'Ansprechpartner (wird nicht veröffentlicht)',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
                'attr' => [
                    'maxlength' => 500,
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Homepage',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('jobsUrl', TextType::class, [
                'label' => 'Stellenangebote oder Karriere-Seite',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('facebookUrl', TextType::class, [
                'label' => 'Facebook-Seite',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('twitterUrl', TextType::class, [
                'label' => 'Twitter-Profil',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('youtubeUrl', TextType::class, [
                'label' => 'Youtube-Profil',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('instagramUrl', TextType::class, [
                'label' => 'Instagram-Profil',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('xingUrl', TextType::class, [
                'label' => 'Xing-Profil',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('linkedinUrl', TextType::class, [
                'label' => 'LinkedIn-Profil',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganizationDetail::class,
            'attr' => ['autocomplete' => 'off'],
        ]);
    }
}
