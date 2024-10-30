<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MotDuJour;

class TelephoneController extends AbstractController
{
    #[Route('/telephone', name: 'app_telephone')]
    public function index(): Response
    {
        return $this->render('telephone/index.html.twig', [
            'controller_name' => 'TelephoneController',
        ]);
    }
    #[Route('/telephone/decode', name: 'app_telephone_decode')]
    public function decode(Request $request): Response
    {
        $numbers = $request->query->get('numbers');
        return $this->render('telephone/decode.html.twig', ['numbers' => $numbers]);
    }
    #[Route('/telephone/finish', name: 'app_telephone_finish')]
    public function finish(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motDuJour = $entityManager->getRepository(MotDuJour::class)->findOneBy([], ['id' => 'DESC']);
        $motRentre = $request->request->get('mot');
        dump($motDuJour->getMot());
        dump($motRentre);
        // On compare les deux mots, sans tenir compte des accents, ou des majuscules
        if (strtolower($motDuJour->getMot()) === strtolower($motRentre)) {
            return $this->redirectToRoute('app_telephone_success', ['popup' => 'success', 'motDuJour' => $motDuJour->getMot()]);
        }
        return $this->render('telephone/finish.html.twig', ['mot' => $motRentre]);
    }
    #[Route('/telephone/success', name: 'app_telephone_success')]
    public function success(Request $request): Response
    {
        dump($request->query->get('popup'));
        //si on a rien à faire sur cette page (pas de popup success), on redirige vers l'index telephone
        if ($request->query->get('popup') != 'success') {
            return $this->redirectToRoute('app_telephone');
        }
        else {
            return $this->render('telephone/success.html.twig', ['motDuJour' => $request->query->get('motDuJour')]);
        }
    }
}
