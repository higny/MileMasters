<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/arrivées')]
#[IsGranted('ROLE_USER')]
class ArrivalController extends AbstractController
{
    #[Route('/', name: 'arrival.index')]
    public function index(): Response
    {
        return $this->render('arrival/index.html.twig', [
            'controller_name' => 'ArrivalController',
        ]);
    }
}
