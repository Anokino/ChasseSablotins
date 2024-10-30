<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MotDuJour;
class BorneController extends AbstractController
{
    #[Route('/borne', name: 'app_borne')]
    public function index(): Response
    {
        return $this->render('borne/index.html.twig', [
            'controller_name' => 'BorneController',
        ]);
    }
    #[Route('/borne/question', name: 'app_borne_question')]
    public function question(): Response
    {
        return $this->render('borne/question.html.twig');
    }
    #[Route('/borne/reponse_question', name: 'app_borne_reponse_question', methods: ['POST'])]
    public function reponseQuestion(Request $request): Response
    {
        $reponse = $request->request->get('reponse');
        return $this->render('borne/reponse_question.html.twig', ['reponse' => $reponse]);
    }
    #[Route('/borne/finish', name: 'app_borne_finish')]
    public function finish(Request $request): Response
    {
        // On récupère les nombres sous forme de chaîne JSON si présent
        $numbersJson = $request->query->get('numbers');
        $numbers = $numbersJson ? json_decode($numbersJson, true) : [];
        $motDuJourNombresJson = $request->query->get('motDuJourNombres');
        $motDuJourNombres = $motDuJourNombresJson ? json_decode($motDuJourNombresJson, true) : [];
        
        return $this->render('borne/finish.html.twig', [
            'numbers' => $numbers,
            'sommeNombres' => $request->query->get('sommeNombres'),
            'error' => $request->query->get('error'),
            'motDuJourNombres' => $motDuJourNombres,
            'popup' => $request->query->get('popup')
        ]);
    }
    #[Route('/borne/validate_numbers', name: 'app_borne_validate_numbers', methods: ['POST'])]
    public function validateNumbers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $numbers = $request->request->all()['numbers'] ?? [];
        
        // On récupère le mot du jour
        $motDuJour = $entityManager->getRepository(MotDuJour::class)->findOneBy([], ['id' => 'DESC']);
        
        if (!$motDuJour) {
            throw $this->createNotFoundException('Aucun mot du jour trouvé');
        }
        
        // On convertit le mot du jour en nombres selon le code A1Z26 (A=1, B=2, etc)
        $motDuJourNombres = array_map(function($char) {
            return ord(strtoupper($char)) - ord('A') + 1;
        }, str_split($motDuJour->getMot()));

        // On fait la somme des nombres du mot du jour
        $motDuJourSommeNombres = array_sum($motDuJourNombres);

        // On fait la somme des nombres entrés
        $sommeNombres = array_sum(array_map('intval', $numbers));
        
        // On compare le total des nombres entrés avec le total du mot du jour
        if($sommeNombres == $motDuJourSommeNombres) {
            // Si c'est bon, on retourne sur la vue de fin, avec les nombres du mot du jour dans l'ordre, soit pour chocolat : 3; 8; 3; 15; 12; 12; 15;
            return $this->redirectToRoute('app_borne_finish', [
                'popup' => 'true',
                'motDuJourNombres' => json_encode($motDuJourNombres)
            ]);
        } else {
            // Si c'est pas bon, on redirige vers la page de fin avec un message d'erreur
            return $this->redirectToRoute('app_borne_finish', [
                'error' => 'true',
                'sommeNombres' => $sommeNombres,
                'motDuJour' => $motDuJour->getMot(),
                'sommeMotDuJour' => $motDuJourSommeNombres,
                'numbers' => json_encode($numbers) // On encode le tableau en JSON
            ]);
        }
    }
    #[Route('/borne/coming_soon', name: 'app_borne_coming_soon')]
    public function comingSoon(): Response
    {
        return $this->render('borne/coming_soon.html.twig');
    }
}
