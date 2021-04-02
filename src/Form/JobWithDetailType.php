<?php

namespace App\Form;

use App\DTO\JobWithDetail;
use App\Entity\Organization;
use App\Enum\HomeOfficeEnum;
use App\Enum\OeffiErreichbarkeitEnum;
use App\Enum\GehaltsvorstellungEnum;
use App\Enum\SlackTimeEnum;
use App\Repository\OrganizationRepository;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobWithDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titel',
                'attr' => [
                    'maxlength' => 30,
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Beschreibung (worum geht\'s? was macht die Stelle besonders?)',
                'required' => false,
                'attr' => [
                    'maxlength' => 250,
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Link (z. B. "klassische" Stellenanzeige)',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('fullyRemote', CheckboxType::class, [
                'label' => 'Bei dem Job handelt es sich um eine "fully remote" Stelle (auch Post-Covid)',
                'required' => false,
            ])
            ->add('location', LocationType::class, [
                'label' => 'Unternehmensort',
            ])
            ->add('locationLat', HiddenType::class, ['attr' => ['class' => 'location-lat']])
            ->add('locationLng', HiddenType::class, ['attr' => ['class' => 'location-lng']])
            ->add('homeOffice', EnumType::class, [
                'enum_class' => HomeOfficeEnum::class,
                'label' => 'Home-Office (Post-Covid)',
                'required' => false,
                'placeholder' => '-- keine Angabe --',
            ])
            ->add('oeffiErreichbarkeit', EnumType::class, [
                'enum_class' => OeffiErreichbarkeitEnum::class,
                'label' => 'Öffi-Erreichbarkeit',
                'required' => false,
                'placeholder' => '-- keine Angabe --',
            ])
            ->add('slackTime', EnumType::class, [
                'enum_class' => SlackTimeEnum::class,
                'label' => 'Slack-Time Regelung',
                'required' => false,
                'placeholder' => '-- keine Angabe --',
            ])
            ->add('gehaltsvorstellung', EnumType::class, [
                'enum_class' => GehaltsvorstellungEnum::class,
                'label' => 'Gehaltsvorstellung',
                'required' => false,
                'placeholder' => '-- keine Angabe --',
            ])
            ->add('weiterbildungsbudget', CheckboxType::class, [
                'label' => 'es gibt ein Weiterbildungsbudget',
                'required' => false,
            ])
            ->add('teilzeitPossible', CheckboxType::class, [
                'label' => 'in Teilzeit möglich (z.B. vier statt fünf Tage/Woche)',
                'required' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            /** @var JobWithDetail $jobWithDetail */
            $jobWithDetail = $formEvent->getData();

            $formEvent->getForm()->add('organization', EntityType::class, [
                'label' => 'Arbeitgeber',
                'class' => Organization::class,
                'required' => true,
                'choice_label' => 'title',
                'placeholder' => null,
                'query_builder' => function (OrganizationRepository $repo) use ($jobWithDetail) {
                    return $repo
                        ->createQueryBuilder('o')
                        ->leftJoin('o.proposedOrganizationDetails', 'opd')
                        ->addSelect('opd')
                        ->andWhere('o.owner = :owner')
                        ->setParameter('owner', $jobWithDetail->getOrganization()->getOwner());
                },
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JobWithDetail::class,
            'attr' => ['autocomplete' => 'off'],
            'validation_groups' => function (FormInterface $form) {
                /** @var JobWithDetail $data */
                $data = $form->getData();
                return ['Default', $data->getFullyRemote() ? 'fully_remote_job' : 'office_job'];
            },
        ]);
    }
}
