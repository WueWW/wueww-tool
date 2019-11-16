<?php

namespace App\Form;

use App\Entity\OrganizationDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class OrganizationDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('contactName', TextType::class, [
                'label' => 'Ansprechpartner (wird nicht veröffentlicht)',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
                'attr' => [
                    'maxlength' => 500,
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Link zur Homepage',
                'required' => false,
            ])
            ->add('facebookUrl', TextType::class, [
                'label' => 'Link zur Facebook-Seite',
                'required' => false,
            ])
            ->add('twitterUrl', TextType::class, [
                'label' => 'Link zum Twitter-Profil',
                'required' => false,
            ])
            ->add('youtubeUrl', TextType::class, [
                'label' => 'Link zu Youtube-Profil',
                'required' => false,
            ])
            ->add('instagramUrl', TextType::class, [
                'label' => 'Link zum Instagram-Profil',
                'required' => false,
            ])
            ->add('logo', FileType::class, [
                'label' => 'Neues Logo hochladen (JPEG-Format)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg'],
                        'mimeTypesMessage' => 'Bitte stelle ein Bild im JPEG-Format zur Verfügung.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => OrganizationDetail::class,
            ])
            ->setRequired('currentLogoId');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['currentLogoId'] = $options['currentLogoId'];
    }
}
