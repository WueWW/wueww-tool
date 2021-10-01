<?php

namespace App\Controller;

use App\DTO\LogoUpload;
use App\DTO\OrganizationCreate;
use App\Entity\Organization;
use App\Entity\User;
use App\Event\OrganizationModifiedEvent;
use App\Form\LogoUploadType;
use App\Form\OrganizationCreateType;
use App\Form\OrganizationDetailType;
use App\Repository\OrganizationRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/organization")
 * @method User getUser()
 */
class OrganizationController extends AbstractController
{
    /**
     * @Route("/", name="organization_index", methods={"GET"})
     * @param OrganizationRepository $organizationRepository
     * @return Response
     */
    public function index(Request $request, OrganizationRepository $organizationRepository): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $organizations = $organizationRepository->findAllWithProposedDetails(
                $request->query->has('has_changes'),
                $request->query->has('not_approved')
            );
        } else {
            $organizations = $this->getUser()->getOrganizations();
        }

        return $this->render('organization/index.html.twig', ['organizations' => $organizations]);
    }

    /**
     * @Route("/new", name="organization_new", methods={"GET","POST"})
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organization = new Organization();
        $organization->ensureEditableOrganizationDetails();
        $form = $this->createForm(OrganizationDetailType::class, $organization->getProposedOrganizationDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->addOrganization($organization);
            $this->getDoctrine()
                ->getManager()
                ->flush();

            $eventDispatcher->dispatch(new OrganizationModifiedEvent($organization));
            $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
            return $this->redirectToRoute('organization_index');
        }

        return $this->render('organization/new.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new-with-user", name="userorg_new_by_editor", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function newWithUser(Request $request): Response
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

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $ex) {
                $this->addFlash('danger', 'Für diese E-Mail Adresse besteht bereits ein Veranstalterkonto.');
                return $this->redirectToRoute('organization_index');
            }

            $this->addFlash('success', 'Das Veranstalterkonto wurde angelegt.');

            return $this->redirectToRoute('organization_edit', ['id' => $organization->getId()]);
        }

        return $this->render('organization/new-with-user.html.twig', [
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
        if (!$this->isGranted(User::ROLE_EDITOR) && $organization->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('organization/show.html.twig', [
            'organization' => $organization,
        ]);
    }

    /**
     * @Route("/{id}/diff", name="organization_diff", methods={"GET"})
     * @param Organization $organization
     * @return Response
     */
    public function diff(Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if (!$organization->isAcceptedAndChanged()) {
            throw new \LogicException('cannot diff a non-accepted organization');
        }

        return $this->render('organization/diff.html.twig', [
            'organization' => $organization,
        ]);
    }

    /**
     * @Route("/{id}/logo", name="organization_logo", methods={"GET","POST"})
     * @param RequestStack $requestStack
     * @param Organization $organization
     * @return Response
     */
    public function logo(RequestStack $requestStack, Organization $organization): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $dto = new LogoUpload();
        $dto->setMasterRequestUri(
            $requestStack->getMasterRequest()->getBasePath() . $requestStack->getMasterRequest()->getPathInfo()
        );

        $form = $this->createForm(LogoUploadType::class, $dto, [
            'action' => $this->generateUrl('organization_logo', ['id' => $organization->getId()]),
        ]);

        $form->handleRequest($requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile $jpgFile */
                $jpgFile = $form['file']->getData();
                $newName = uniqid() . '.' . $jpgFile->guessExtension();
                $jpgFile->move($this->getParameter('logos_directory'), $newName);

                $organization->setLogoFileName($newName);

                $this->getDoctrine()
                    ->getManager()
                    ->flush();

                $this->addFlash('success', 'Das Logo wurde hinterlegt.');
            } else {
                $this->addFlash('warning', 'Der Logo-Upload ist fehlgeschlagen.');
            }

            return $this->redirect($dto->getMasterRequestUri());
        }

        return $this->render('organization/logo.html.twig', [
            'current_logo' => $organization->getLogoFileName()
                ? \sprintf(
                    '%s/logos/%s',
                    $requestStack->getMasterRequest()->getBasePath(),
                    $organization->getLogoFileName()
                )
                : null,
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organization_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Organization $organization
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function edit(
        Request $request,
        Organization $organization,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        if (!$this->isGranted(User::ROLE_EDITOR) && $organization->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $organization->ensureEditableOrganizationDetails();
        $form = $this->createForm(OrganizationDetailType::class, $organization->getProposedOrganizationDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted(User::ROLE_EDITOR)) {
                $organization->accept();
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } else {
                $eventDispatcher->dispatch(new OrganizationModifiedEvent($organization));
                $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');

                if (!$organization->getLogoFileName()) {
                    $this->addFlash(
                        'warning',
                        'Sofern nicht bereits geschehen, sende bitte noch eine Logo an kontakt@wueww.de. Bevorzugte Formate: EPS, PDF, AI, ggf. hochauflösendes JPEG.'
                    );
                }
            }

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

            $this->addFlash('success', 'Die Veranstalterinformationen wurden freigegeben.');
        }

        return $this->redirectToRoute('organization_index');
    }
}
