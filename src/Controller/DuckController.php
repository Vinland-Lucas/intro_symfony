<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Form\DuckType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/signup', name: 'sign_up')]
class DuckController extends AbstractController
{
//    #[Route('/duck', name: 'app_duck')]
//    public function index(): Response
//    {
//        return $this->render('duck/index.html.twig', [
//            'controller_name' => 'DuckController',
//        ]);
//    }

    #[Route('/', name: 'create_duck', methods: ['GET', 'POST'])]
    public function createDuck(EntityManagerInterface $entityManager, Request $request): Response
    {
        $duck = new Duck();

        $form = $this->createForm(DuckType::class, $duck);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $duck = $form->getData();

            // tell Doctrine you want to (eventually) save the Duck (no queries yet)
            $entityManager->persist($duck);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirectToRoute('/app_quack');
        }

        return $this->render('duck/signup.html.twig', [
            'form' => $form
        ]);
    }

}
