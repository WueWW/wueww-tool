<?php

namespace App\Form;

use App\Entity\Feedback;
use blackknight467\StarRatingBundle\Form\RatingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('starRating', RatingType::class, [
                'label' => 'Star Rating',
                'required' => false,
            ])
            ->add('message', TextAreaType::class, [
                'label' => 'Weitere Bemerkungen',
                'required' => false,
            ])
            ->add('author', TextType::class, [
                'label' => 'Name/E-Mail (optional)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
