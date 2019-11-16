<?php

namespace App\Controller;

use App\DTO\OrganizationCreate;
use App\Entity\Organization;
use App\Entity\OrganizationDetail;
use App\Entity\User;
use App\Form\OrganizationCreateType;
use App\Form\OrganizationDetailType;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/organization")
 */
class OrganizationController extends AbstractController
{
    /**
     * @Route("/", name="organization_index", methods={"GET"})
     * @param OrganizationRepository $organizationRepository
     * @return Response
     */
    public function index(OrganizationRepository $organizationRepository): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organizations = $organizationRepository->findAll();

        return $this->render('organization/index.html.twig', ['organizations' => $organizations]);
    }

    /**
     * @Route("/new", name="organization_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organizationCreateDTO = new OrganizationCreate();

        $form = $this->createForm(OrganizationCreateType::class, $organizationCreateDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization = new Organization();
            $user = (new User())
                ->setEmail($organizationCreateDTO->getEmail())
                ->setPassword('!')
                ->setRegistrationComplete(true)
                ->addOrganization($organization);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('organization_edit', ['id' => $organization->getId()]);
        }

        return $this->render('organization/new.html.twig', [
            'organization' => $organizationCreateDTO,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_show", methods={"GET"})
     * @param Organization $organization
     * @return Response
     */
    public function show(Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        return $this->render('organization/show.html.twig', [
            'organization' => $organization,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organization_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Organization $organization
     * @return Response
     */
    public function edit(Request $request, Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organization->ensureEditableOrganizationDetails();
        $form = $this->createForm(OrganizationDetailType::class, $organization->getProposedOrganizationDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization->accept();
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('organization_index');
        }

        return $this->render('organization/edit.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_delete", methods={"DELETE"})
     * @param Request $request
     * @param Organization $organization
     * @return Response
     */
    public function delete(Request $request, Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $organization->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($organization);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organization_index');
    }

    /**
     * @Route("/{id}/accept", name="organization_accept", methods={"POST"})
     * @param Request $request
     * @param Organization $organization
     * @return Response
     */
    public function accept(Request $request, Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('accept' . $organization->getId(), $request->request->get('_token'))) {
            $organization->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('organization_index');
    }
}
