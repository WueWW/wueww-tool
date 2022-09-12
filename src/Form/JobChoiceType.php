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
            'Fachinformatiker (m/w/d)' => [
                'Fachinformatiker/-in – Fachrichtung: Anwendungsentwicklung' =>
                    'Fachinformatiker/-in – Fachrichtung: Anwendungsentwicklung',
                'Fachinformatiker/-in – Fachrichtung: Daten- und Prozessanalyse' =>
                    'Fachinformatiker/-in – Fachrichtung: Daten- und Prozessanalyse',
                'Fachinformatiker/-in – Fachrichtung: Digitale Vernetzung' =>
                    'Fachinformatiker/-in – Fachrichtung: Digitale Vernetzung',
                'Fachinformatiker/-in – Fachrichtung: Systemintegration' =>
                    'Fachinformatiker/-in – Fachrichtung: Systemintegration',
            ],
            'Technische Berufe' => [
                'Elektroniker/-in für Informations- und Systemtechnik' =>
                    'Elektroniker/-in für Informations- und Systemtechnik',
                'IT-System Elektroniker/-in' => 'IT-System Elektroniker/-in',
            ],
            'Kaufleute' => [
                'Kaufmann/-frau für audiovisuelle Medien' => 'Kaufmann/-frau für audiovisuelle Medien',
                'Kaufmann/-frau für Digitalisierungsmanagement' => 'Kaufmann/-frau für Digitalisierungsmanagement',
                'Kaufmann/-frau für IT-System-Management' => 'Kaufmann/-frau für IT-System-Management',
                'Kaufmann/-frau für Marketingkommunikation' => 'Kaufmann/-frau für Marketingkommunikation',
                'Kaufmann/-frau im E-CommerceMedienkaufmann/-frau Digital und Print' =>
                    'Kaufmann/-frau im E-CommerceMedienkaufmann/-frau Digital und Print',
            ],
            'Weitere Berufe' => [
                'Fachangestellte/-r für Medien- und Informationsdienste' =>
                    'Fachangestellte/-r für Medien- und Informationsdienste',
                'Mediengestalter/-in Digital und Print' => 'Mediengestalter/-in Digital und Print',
            ],
        ]);

        $resolver->setDefault('expanded', true);
        $resolver->setDefault('multiple', true);
    }
}
