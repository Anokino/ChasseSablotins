<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Questions;
class QuestionsController extends AbstractController
{
    #[Route('/questions', name: 'app_questions')]
    public function index(): Response
    {
        return $this->render('questions/index.html.twig', [
            'controller_name' => 'QuestionsController',
        ]);
    }

    #[Route('/question/{id}', name: 'app_question')]
    public function question(int $id, EntityManagerInterface $entityManager): Response
    {
        $question = $entityManager->getRepository(Questions::class)->find($id);
        return $this->render('questions/question.html.twig', [
            'question' => $question,
        ]);
    }
}
