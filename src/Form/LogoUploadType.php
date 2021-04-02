<?php

namespace App\Form;

use App\DTO\LogoUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LogoUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('masterRequestUri', HiddenType::class)->add('file', FileType::class, [
            'label' => 'Neues Logo hochladen (JPEG-Format)',
            'required' => true,
            'mapped' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => ['image/jpeg'],
                    'mimeTypesMessage' => 'Bitte stelle ein Bild im JPEG-Format zur Verfügung.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LogoUpload::class,
        ]);
    }
}
