<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobChoiceType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('choices', [
            'Fachinformatiker/-in' => [
                'Fachrichtung Anwendungsentwicklung' => 'Fachrichtung Anwendungsentwicklung',
                'Fachrichtung Systemintegration' => 'Fachrichtung Systemintegration',
                'Fachrichtung Digitale Vernetzung' => 'Fachrichtung Digitale Vernetzung',
                'Fachrichtung Daten- und Prozessanalyse' => 'Fachrichtung Daten- und Prozessanalyse',
            ],
            'Kaufleute' => [
                'Kaufleute für IT-System-Management' => 'Kaufleute für IT-System-Management',
                'Kaufleute für Digitalisierungsmanagement' => 'Kaufleute für Digitalisierungsmanagement',
            ],
        ]);
        $resolver->setDefault('expanded', true);
        $resolver->setDefault('multiple', true);
    }
}
