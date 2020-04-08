<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionWithDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', TextType::class, [
            'label' => 'Datum',
        ]);

        $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer(null, null, 'd.m.Y'));

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
                'required' => false,
                'attr' => [
                    'maxlength' => 250,
                ],
            ])
            ->add('longDescription', TextareaType::class, [
                'label' => 'Langbeschreibung (für die Webseite, WueWW App etc.)',
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'label' => 'Link zum Veranstaltungsort (z. B. Zoom, Google Meet etc.)',
                'attr' => [
                    'maxlength' => 255,
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Link (z. B. Anmeldeseite, weitere Informationen zur Veranstaltung etc.)',
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SessionWithDetail::class,
            'attr' => ['autocomplete' => 'off'],
        ]);
    }
}
