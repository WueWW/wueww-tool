<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Session;
use App\Entity\User;
use App\Form\FeedbackType;
use App\Service\FeedbackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/feedback")
 * @method User getUser()
 */
class FeedbackController extends AbstractController
{
    /**
     * @Route("/session/{id}/post", name="feedback_post", methods={"GET","POST"})
     * @param Session $session
     * @param Request $request
     * @return Response
     */
    public function post(Session $session, Request $request): Response
    {
        $feedback = (new Feedback())->setSession($session);

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('feedback_thanks');
        }

        return $this->render('feedback/post.html.twig', [
            'form' => $form->createView(),
            'session' => $session,
        ]);
    }

    /**
     * @Route("/thanks", name="feedback_thanks", methods={"GET"})
     */
    public function thanks()
    {
        return $this->render('feedback/thanks.html.twig');
    }

    /**
     * @Route("/session/{id}/pdf", name="feedback_pdf", methods={"GET"})
     * @param Session $session
     * @param FeedbackService $feedbackService
     * @return Response
     */
    public function pdf(Session $session, FeedbackService $feedbackService): Response
    {
        return Response::create($feedbackService->generatePdf($session), Response::HTTP_OK, [
            'Content-type' => 'application/pdf',
        ]);
    }
}
