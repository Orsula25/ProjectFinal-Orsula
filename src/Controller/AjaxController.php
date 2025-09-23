<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AjaxController extends AbstractController
{
    #[Route('/ajax/form/independent', name: 'app_ajax_independent')]
    public function formIndependent(): Response
    {
        return $this->render('ajax/form_Independent.html.twig');
    }

    #[Route('/ajax/form/independent/traitement', name: 'app_ajax_independent_traitement')]
    public function  formIndependentTraitement(Request $req):Response
    {   
        $nom = $req->get('name');

        $vars = ['message' => 'Bonjour' . "<br>" . $nom,
                'autredonnees' => 'autres'];

        return new JsonResponse($vars);


    }
}

