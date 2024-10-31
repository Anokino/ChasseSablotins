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
        $motDuJour = $entityManager->getRepository(MotDuJour::class)->findOneBy([], ['id' => 'DESC']);
        $motDuJourNombres = implode(';', array_map(function($char) {
            return ord(strtoupper($char)) - 64;
        }, str_split($motDuJour->getMot())));
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
            'mot_du_jour' => $motDuJour,
            'questions' => $entityManager->getRepository(Questions::class)->findAll(),
            'mot_du_jour_nombres' => $motDuJourNombres
            
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
        
        // Vérification du nombre de questions existantes
        $questions = $entityManager->getRepository(Questions::class)->findAll();
        if (count($questions) < 12) {
            // Redirection vers la route de réinitialisation des questions si moins de 12 existent
            $this->addFlash('info', 'Réinitialisation des questions en cours...');
            return $this->redirectToRoute('app_back_office_reset_questions');
        }
        
        // Attribution des nombres du mot aux questions
        $lettres = str_split($mot);
        $valeurs = array_map(function($lettre) {
            return ord(strtoupper($lettre)) - 64;
        }, $lettres);
        
        $questions = $entityManager->getRepository(Questions::class)->findAll();
        $nombreQuestions = count($questions);
        $nombreValeurs = count($valeurs);
        
        // Attribution des valeurs du mot aux questions
        for ($i = 0; $i < $nombreValeurs; $i++) {
            $question = $questions[$i];
            $question->setNombre([$valeurs[$i]]);
            $entityManager->persist($question);
        }
        
        // Attribution du bonus aux questions restantes
        $nombreQuestionsRestantes = $nombreQuestions - $nombreValeurs;
        $nombreQuestionsBonus = min(10, $nombreQuestionsRestantes); // Maximum de 10 questions en bonus
        for ($i = $nombreValeurs; $i < $nombreValeurs + $nombreQuestionsBonus; $i++) {
            $question = $questions[$i];
            $question->setNombre(['bonus']);
            $entityManager->persist($question);
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Mot du jour mis à jour avec succès !');
        dump();
        return $this->redirectToRoute('app_back_office');
    }
    
    #[Route('/backoffice/reset_questions', name: 'app_back_office_reset_questions', methods: ['POST'])]
    public function resetQuestions(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les questions existantes
        $questions = $entityManager->getRepository(Questions::class)->findAll();
    
        if (count($questions) < 12) {
            // Créer des questions si moins de 12 existent
            for ($i = 1; $i <= 12; $i++) {
                $question = new Questions();
                $question->setQuestion(''); // Vous pouvez définir une question par défaut si nécessaire
                $question->setReponse('');
                $question->setNombre([]);
                $entityManager->persist($question);
            }
        } else {
            // Mettre à NULL les champs pour les 12 questions existantes
            foreach ($questions as $question) {
                $question->setQuestion(null);
                $question->setReponse(null);
                $question->setNombre(null);
                $entityManager->persist($question);
            }
        }
    
        // Enregistrer les changements dans la base de données
        $entityManager->flush();
    
        return $this->redirectToRoute('app_back_office');
    }

    #[Route('/backoffice/modif_question/{id}', name: 'app_back_office_modif_question', methods: ['POST'])]
    public function modifQuestion(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $question = $entityManager->getRepository(Questions::class)->find($id);
        
        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $question->setQuestion($request->request->get('question'));
        $question->setReponse($request->request->get('reponse'));
        $nombreString = $request->request->get('nombre');
        $nombreArray = explode(',', $nombreString);
        $question->setNombre($nombreArray);
        
        $entityManager->persist($question);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Question modifiée avec succès.');

        return new Response('Question modifiée avec succès.', Response::HTTP_OK);
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
