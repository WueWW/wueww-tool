<?php

namespace App\Controller;

use App\DTO\JobWithDetail;
use App\Entity\Job;
use App\Entity\User;
use App\Event\JobDeletedEvent;
use App\Event\JobModifiedEvent;
use App\Form\JobWithDetailType;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/job")
 * @method User getUser()
 */
class JobController extends AbstractController
{
    /**
     * @Route("/", name="job_index", methods={"GET"})
     * @param JobRepository $jobRepository
     * @return Response
     */
    public function index(JobRepository $jobRepository): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            $jobs = $jobRepository->findAllWithProposedDetails();
        } else {
            $jobs = $jobRepository->findByUser($this->getUser());
        }

        return $this->render('job/index.html.twig', ['jobs' => $jobs]);
    }

    /**
     * @Route("/new", name="job_new", methods={"GET","POST"})
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($this->isGranted(User::ROLE_EDITOR)) {
            throw new \LogicException('job_new route not expected to be called by editor');
        }

        $jobWithDetail = (new JobWithDetail())->setOrganization(
            $this->getUser()
                ->getOrganizations()
                ->first()
        );

        $form = $this->createForm(JobWithDetailType::class, $jobWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = (new Job())->applyDetails($jobWithDetail);

            if ($job->getOrganization()->getOwner() !== $this->getUser()) {
                throw new AccessDeniedException();
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            if ($this->isGranted(User::ROLE_EDITOR)) {
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } else {
                $eventDispatcher->dispatch(new JobModifiedEvent($job));
                $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
            }

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/new.html.twig', [
            'job' => $jobWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_show", methods={"GET"})
     * @param Job $job
     * @return Response
     */
    public function show(Job $job): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR) && $job->getOrganization()->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="job_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Job $job
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function edit(Request $request, Job $job, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR) && $job->getOrganization()->getOwner() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $jobWithDetail = $job->toJobWithDetail();

        $form = $this->createForm(JobWithDetailType::class, $jobWithDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->applyDetails($jobWithDetail);

            if ($this->isGranted(User::ROLE_EDITOR)) {
                $job->accept();
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } elseif ($job->getOrganization()->getOwner() !== $this->getUser()) {
                throw new AccessDeniedException();
            } elseif ($job->getAcceptedDetails() === $job->getProposedDetails()) {
                $this->addFlash('success', 'Die Änderungen wurden gespeichert.');
            } else {
                $eventDispatcher->dispatch(new JobModifiedEvent($job));
                $this->addFlash('success', 'Die Änderungen wurden gespeichert und zum Review eingereicht.');
            }

            $this->getDoctrine()
                ->getManager()
                ->flush();
            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/edit.html.twig', [
            'job' => $jobWithDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_delete", methods={"DELETE"})
     * @param Request $request
     * @param Job $job
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function delete(Request $request, Job $job, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($this->isCsrfTokenValid('delete' . $job->getId(), $request->request->get('_token'))) {
            if (!$this->isGranted(User::ROLE_EDITOR)) {
                if ($job->getOrganization()->getOwner() !== $this->getUser()) {
                    throw new AccessDeniedException();
                }

                $eventDispatcher->dispatch(new JobDeletedEvent($job));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();

            $this->addFlash('success', 'Der Job wurde gelöscht.');
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * @Route("/{id}/accept", name="job_accept", methods={"POST"})
     * @param Request $request
     * @param Job $job
     * @return Response
     */
    public function accept(Request $request, Job $job): Response
    {
        if (!$this->isGranted(User::ROLE_EDITOR)) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('accept' . $job->getId(), $request->request->get('_token'))) {
            $job->accept();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Der Job wurde freigegeben.');
        }

        return $this->redirectToRoute('job_index');
    }
}
