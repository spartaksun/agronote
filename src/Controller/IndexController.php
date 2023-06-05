<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

}
