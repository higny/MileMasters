<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArrivalController extends AbstractController
{
    #[Route('/arrival', name: 'arrival.index')]
    public function index(): Response
    {
        return $this->render('arrival/index.html.twig', [
            'controller_name' => 'ArrivalController',
        ]);
    }
}
