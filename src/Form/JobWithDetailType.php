<?php

namespace App\Form;

use App\DTO\JobWithDetail;
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
                'label' => 'Kurzbeschreibung (für Social Media)',
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
        ]);
    }
}
