<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\OrganizationModifiedEvent;
use App\Form\OrganizationDetailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class MyOrganizationController extends AbstractController
{
    /**
     * @Route("/my-organization", name="my_organization")
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function edit(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        if (
            $this->getUser()
                ->getOrganizations()
                ->count() > 1
        ) {
            return $this->forward(OrganizationController::class . ':index');
        }

        $organization = $this->getUser()
            ->getOrganizations()
            ->first();

        $organization->ensureEditableOrganizationDetails();

        $form = $this->createForm(OrganizationDetailType::class, $organization->getProposedOrganizationDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            $eventDispatcher->dispatch(new OrganizationModifiedEvent($organization));
            $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
        }

        return $this->render('my_organization/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
