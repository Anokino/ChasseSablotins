<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MotDuJour;

class MainController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_main');
    }
    #[Route('/main', name: 'app_main')]
    public function main(): Response
    {
        return $this->render('main/main.html.twig');
    }
}
