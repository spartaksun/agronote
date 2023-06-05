<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskSearchType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateInterval;
use DateTime;
use Exception;
use joshtronic\LoremIpsum;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PaginatorInterface $paginator, TaskRepository $taskRepository): Response
    {
        $limitPerPage = 5;
        $form = $this->createForm(TaskSearchType::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);
        $createdBy = in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ? null : $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $queryBuilder = $taskRepository->getQbSearchByParams(
                createdBy: $createdBy,
                sortBy: $form->get('sortBy')->getData(),
                status: $form->get('status')->getData(),
                search: $form->get('search')->getData(),
                onlyNotExpired: true
            );

        } else {
            $queryBuilder = $taskRepository->getQbSearchByParams(
                createdBy: $createdBy,
            );
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $limitPerPage
        );

        return $this->render('task/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedBy($this->getUser());
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $this->checkPermissions($task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTimeImmutable('now'));
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $this->checkPermissions($task);

        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    private function checkPermissions(Task $task) {
        if ($task->getCreatedBy() !== $this->getUser() && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw $this->createAccessDeniedException();
        }
    }

}
