<?php

namespace Celtic34fr\CalendarCore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalTypeParameterController extends AbstractController
{
    #[Route('/cal/type/parameter', name: 'app_cal_type_parameter')]
    public function index(): Response
    {
        return $this->render('@calendar-core/parameter/cal_type/create.html.twig', [
            'controller_name' => 'CalTypeParameterController',
        ]);
    }
}
