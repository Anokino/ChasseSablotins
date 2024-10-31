<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\MotDuJour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Questions;

class BackOfficeController extends AbstractController
{
    #[Route('/backoffice', name: 'app_back_office')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
            'mot_du_jour' => $entityManager->getRepository(MotDuJour::class)->findOneBy([], ['id' => 'DESC']),
            'questions' => $entityManager->getRepository(Questions::class)->findAll()
            
        ]);
    }
    #[Route('/backoffice/modif_mot', name: 'app_back_office_modif_mot', methods: ['POST'])]
    public function modifMot(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mot = $request->request->get('mot');
        $motDuJour = $entityManager->getRepository(MotDuJour::class)->findOneBy([], ['id' => 'DESC']);
        
        if (!$motDuJour) {
            // Création d'un nouveau mot du jour s'il n'en existe pas
            $motDuJour = new MotDuJour();
        } else {
            // Suppression des anciens mots du jour s'il y en a
            $anciensMots = $entityManager->getRepository(MotDuJour::class)->findAll();
            foreach ($anciensMots as $ancienMot) {
                $entityManager->remove($ancienMot);
            }
        }
        
        $motDuJour->setMot($mot);
        $entityManager->persist($motDuJour);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_back_office');
    }
    #[Route('/backoffice/modif_question/{id}', name: 'app_back_office_modif_question', methods: ['POST'])]
    public function modifQuestion(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $question = $entityManager->getRepository(Questions::class)->find($id);
        $question->setQuestion($request->request->get('question'));
        $entityManager->persist($question);
        $entityManager->flush();
        return $this->redirectToRoute('app_back_office');
    }
    #[Route('/backoffice/delete_question/{id}', name: 'app_back_office_delete_question', methods: ['POST'])]
    public function deleteQuestion(EntityManagerInterface $entityManager, int $id): Response
    {
        $question = $entityManager->getRepository(Questions::class)->find($id);
        $entityManager->remove($question);
        $entityManager->flush();
        return $this->redirectToRoute('app_back_office');
    }
}
