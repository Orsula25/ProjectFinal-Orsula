<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReapproController extends AbstractController
{
    #[Route('/reappro', name: 'app_reappro')]
    public function index(): Response
    {
        return $this->render('reappro/index.html.twig', [
            'controller_name' => 'ReapproController',
        ]);
    }
}
