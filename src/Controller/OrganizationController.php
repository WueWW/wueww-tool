<?php


namespace App\Controller;


use App\Entity\OrganizationDetail;
use App\Entity\Session;
use App\Entity\User;
use App\Form\OrganizationDetailType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/organization")
 */
class OrganizationController extends AbstractController
{

    /**
     * @Route("/", name="organization_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        $organizations = $userRepository->findAllReporters();

        return $this->render('organization/index.html.twig', ['organizations' => $organizations,]);
    }

    /**
     * @Route("/{id}", name="organization_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($user->isEditor()) {
            throw new BadRequestHttpException('Referenced User is not of reporter-type');
        }

        return $this->render('organization/show.html.twig', [
            'organization' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organization_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($user->isEditor()) {
            throw new BadRequestHttpException('Referenced User is not of reporter-type');
        }

        $currentLogoId = $user->getProposedOrganizationDetails() ? $user->getProposedOrganizationDetails()->getId() : null;
        $user->ensureEditableOrganizationDetails();
        $form = $this->createForm(OrganizationDetailType::class, $user->getProposedOrganizationDetails(), [
            'currentLogoId' => $currentLogoId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form['logo']->getData();

            if ($logoFile) {
                $user->getProposedOrganizationDetails()->setLogoBlob(file_get_contents($logoFile->getPathname()));
            }

            $user->accept();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organization_index');
        }

        return $this->render('organization/edit.html.twig', [
            'organization' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($user->isEditor()) {
            throw new BadRequestHttpException('Referenced User is not of reporter-type');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organization_index');
    }

    /**
     * @Route("/{id}/accept", name="organization_accept", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function accept(Request $request, User $user): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($user->isEditor()) {
            throw new BadRequestHttpException('Referenced User is not of reporter-type');
        }

        if ($this->isCsrfTokenValid('accept' . $user->getId(), $request->request->get('_token'))) {
            $user->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('organization_index');
    }

    /**
     * @Route("/logo/{id}", name="logo_download", methods={"GET"})
     * @param OrganizationDetail $organizationDetail
     * @return Response
     */
    public function logoDownload(OrganizationDetail $organizationDetail): Response
    {
        if ($organizationDetail->getLogoBlob() === null) {
            return Response::create('', Response::HTTP_NOT_FOUND);
        }

        return new StreamedResponse(function () use ($organizationDetail) {
            fpassthru($organizationDetail->getLogoBlob());
            exit();
        }, 200, [
            'Content-Transfer-Encoding', 'binary',
            'Content-type' => 'image/jpeg',
        ]);
    }
}