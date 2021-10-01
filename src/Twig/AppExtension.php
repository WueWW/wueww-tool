<?php

namespace App\Twig;

use cogpowered\FineDiff\Diff;
use cogpowered\FineDiff\Granularity\Word;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('diff', [$this, 'diff'])];
    }

    public function diff($newValue, $oldValue)
    {
        $htmlString = (new Diff(new Word()))->render($oldValue, $newValue);
        return new Markup($htmlString, 'UTF-8');
    }
}
