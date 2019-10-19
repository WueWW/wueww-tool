<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organization")
 */
class OrganizationController extends AbstractController
{

    /**
     * @Route("/", name="organization_index", methods={"GET"})
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        $organizations = $userRepository->findAllReporters();

        return $this->render('organization/index.html.twig', ['organizations' => $organizations,]);
    }

}