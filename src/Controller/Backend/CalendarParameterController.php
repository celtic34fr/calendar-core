<?php

namespace Celtic34fr\CalendarCore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/parameter/calendar', name: 'parameter-calendar-')]
class CalendarParameterController extends AbstractController
{
    #[Route('/new', name: 'create')]
    public function index(): Response
    {
        return $this->render('@calendar-core/parameter/calendar/create.html.twig', [
            'controller_name' => 'CalendarParameterController',
        ]);
    }
}
