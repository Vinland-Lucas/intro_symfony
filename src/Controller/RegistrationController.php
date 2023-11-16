<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/signup', name: 'sign_up')]
class RegistrationController extends AbstractController
{
//    #[Route('/registration', name: 'app_registration')]
//    public function index(): Response
//    {
//        return $this->render('registration/index.html.twig', [
//            'controller_name' => 'RegistrationController',
//        ]);
//    }

    #[Route('/', name: 'create_user', methods: ['GET', 'POST'])]
    public function createUserAccount(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $plaintextPassword = $user->getPassword();

            // encode the plain password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);

            // tell Doctrine you want to (eventually) save the Duck (no queries yet)
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirectToRoute('timeline');
        }

        return $this->render('registration/signup.html.twig', [
            'form' => $form
        ]);
    }



}
