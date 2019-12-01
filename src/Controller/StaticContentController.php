<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StaticContentController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     */
    public function index()
    {
        return $this->render('static_content/welcome.html.twig');
    }

    /**
     * @Route("/impressum", name="impressum")
     */
    public function impressum()
    {
        return $this->render('static_content/impressum.html.twig');
    }

    /**
     * @Route("/nutzungsbedingungen", name="nutzungsbedingungen")
     */
    public function nutzungsbedingungen()
    {
        return $this->render('static_content/nutzungsbedingungen.html.twig');
    }
}
