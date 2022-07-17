<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
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

class SessionWithDetailType extends AbstractType
{
    /**
     * @var bool
     */
    private $useFreeDateInput;

    public function __construct(ParameterBagInterface $params)
    {
        $this->useFreeDateInput = $params->get('app_free_date_input');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->useFreeDateInput) {
            $builder->add('date', TextType::class, [
                'label' => 'Datum',
                'attr' => [
                    'placeholder' => 'TT.MM.JJJJ',
                ],
            ]);

            $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer(null, null, 'd.m.Y'));
        } else {
            $builder->add('date', ChoiceType::class, [
                'label' => 'Datum',
                'choices' => [
                    'Freitag, 21. Oktober 2022' => '2022-10-21',
                    'Samstag, 22. Oktober 2022' => '2022-10-22',
                    'Sonntag, 23. Oktober 2022' => '2022-10-23',
                    'Montag, 24. Oktober 2022' => '2022-10-24',
                    'Dienstag, 25. Oktober 2022' => '2022-10-25',
                    'Mittwoch, 26. Oktober 2022' => '2022-10-26',
                    'Donnerstag, 27. Oktober 2022' => '2022-10-27',
                    'Freitag, 28. Oktober 2022' => '2022-10-28',
                ],
            ]);

            $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d'));
        }

        $builder
            ->add('start', TimeType::class, [
                'label' => 'Beginn (z. B. 18:00)',
                'widget' => 'single_text',
                'html5' => false,
                'invalid_message' =>
                    'Die erfasste Uhrzeit ist ungültig, bitte nur Stunden und Minuten eingeben (z. B. 18:00).',
            ])
            ->add('stop', TimeType::class, [
                'label' => 'Ende',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'invalid_message' =>
                    'Die erfasste Uhrzeit ist ungültig, bitte nur Stunden und Minuten eingeben (z. B. 18:00).',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titel',
                'attr' => [
                    'maxlength' => 30,
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Kurzbeschreibung (für Social Media)',
                'attr' => [
                    'maxlength' => 250,
                ],
            ])
            ->add('longDescription', TextareaType::class, [
                'label' => 'Langbeschreibung (für die Webseite, WueWW App etc.)',
            ])
            ->add('onlineOnly', CheckboxType::class, [
                'label' => 'Die Veranstaltung findet ausschließlich online statt',
                'required' => false,
            ])
            ->add('location', LocationType::class, [
                'label' => 'Veranstaltungsort',
            ])
            ->add('locationLat', HiddenType::class)
            ->add('locationLng', HiddenType::class)
            ->add('link', TextType::class, [
                'label_html' => true,
                'label' =>
                    'Link oder E-Mail-Adresse (z. B. Anmeldeseite, weitere Informationen zur Veranstaltung etc.)<br/>' .
                    'Wichtig, URLs müssen beginnend mit https:// erfasst werden.',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            /** @var SessionWithDetail $sessionWithDetail */
            $sessionWithDetail = $formEvent->getData();

            $formEvent->getForm()->add('organization', EntityType::class, [
                'label' => 'Veranstalter',
                'class' => Organization::class,
                'required' => true,
                'choice_label' => 'title',
                'placeholder' => null,
                'query_builder' => function (OrganizationRepository $repo) use ($sessionWithDetail) {
                    return $repo
                        ->createQueryBuilder('o')
                        ->leftJoin('o.proposedOrganizationDetails', 'opd')
                        ->addSelect('opd')
                        ->andWhere('o.owner = :owner')
                        ->setParameter('owner', $sessionWithDetail->getOrganization()->getOwner());
                },
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionWithDetail::class,
            'attr' => ['autocomplete' => 'off'],
            'validation_groups' => function (FormInterface $form) {
                /** @var SessionWithDetail $data */
                $data = $form->getData();
                return ['Default', $data->getOnlineOnly() ? 'online_only_event' : 'offline_event'];
            },
        ]);
    }
}
