<?php

namespace App\Controller;

use App\DTO\ApprenticeshipWithDetail;
use App\Entity\Apprenticeship;
use App\Entity\User;
use App\Event\ApprenticeshipModifiedEvent;
use App\Form\ApprenticeshipWithDetailType;
use App\Repository\ApprenticeshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @method User getUser()
 */
#[Route('/apprenticeship')]
class ApprenticeshipController extends AbstractController
{
    #[Route('/', name: 'apprenticeship_index', methods: 'GET')]
    public function index(Request $request, ApprenticeshipRepository $apprenticeshipRepository): Response {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $apprenticeships = $apprenticeshipRepository->findAllWithProposedDetails(
                $request->query->has('has_changes'),
                $request->query->has('not_approved')
            );
        } else {
            $apprenticeships = $apprenticeshipRepository->findByOwner($this->getUser());
        }

        return $this->render('apprenticeship/index.html.twig', ['apprenticeships' => $apprenticeships]);
    }

    #[Route('/new', name: 'apprenticeship_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            throw new \LogicException('apprenticeship_new route not expected to be called by editor');
        }

        $apprenticeshipWithDetail = (new ApprenticeshipWithDetail())->setOwner($this->getUser());

        $form = $this->createForm(ApprenticeshipWithDetailType::class, $apprenticeshipWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apprenticeship = (new Apprenticeship())
                ->applyDetails($apprenticeshipWithDetail)
                ->setOwner($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($apprenticeship);
            $entityManager->flush();

            $eventDispatcher->dispatch(new ApprenticeshipModifiedEvent($apprenticeship));
            $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');

            return $this->redirectToRoute('apprenticeship_index');
        }

        return $this->render('apprenticeship/new.html.twig', [
            'apprenticeship' => $apprenticeshipWithDetail,
            'form' => $form->createView(),
        ]);
    }
}
