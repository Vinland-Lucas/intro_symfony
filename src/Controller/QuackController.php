<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Clock\now;

#[Route('/home', name: 'app_quack')]
class QuackController extends AbstractController
{

//    #[Route('/hello', name: 'app_quack')]
//    public function index(): Response
//    {
//        return $this->render('quack/index.html.twig', [
//            'controller_name' => 'QuackController',
//        ]);
//    }

    #[Route('/quack', name: 'create_quack', methods: ['GET', 'POST'])]
    public function createQuack(EntityManagerInterface $entityManager, Request $request): Response
    {
        $quack = new Quack();

        $form = $this->createForm(QuackType::class, $quack);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $quack = $form->getData();

            // tell Doctrine you want to (eventually) save the Quack (no queries yet)
            $entityManager->persist($quack);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirectToRoute('timeline');
        }

        return $this->render('quack/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'timeline', methods: ['GET'])]
    public function showAllQuacks(EntityManagerInterface $entityManager): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->findAll();

        if (!$quack) {
            throw $this->createNotFoundException(
              'No quacks found here'
            );
        }

        return $this->render('quack/index.html.twig', [
            'all_quacks' => $quack,
        ]);

    }

    #[Route('/{id}', name: "id_quack", methods: ['GET'])]
    public function showQuackById(EntityManagerInterface $entityManager, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for this '.$id
            );
        }

        return $this->render('quack/getquackbyid.html.twig', [
            'quackById' => $quack->getId(),
        ]);
    }


    #[Route('/quack/edit/{id}', name: 'edit_quack', methods: ['GET','PUT', 'POST'])]
    public function editQuack(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);

        $form = $this->createForm(QuackType::class, $quack);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $quack = $form->getData();

            $entityManager->persist($quack);

            $entityManager->flush();

            return $this->redirectToRoute('timeline');
        }

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for id '.$id
            );
        }

//        $quack->setContent("J'ai modifié mon quack !!!");
//        $entityManager->flush();

        return $this->render('quack/editquack.html.twig', [
            'edit' => $quack->getId(),
            'form' => $form
        ]);
    }

    #[Route('/quack/delete/{id}', name: 'delete_quack', methods: ['DELETE', 'POST', 'GET'])]
    public function deleteQuack(EntityManagerInterface $entityManager, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for id '.$id
            );
        }

        $entityManager->remove($quack);
        $entityManager->flush();

        return $this->redirectToRoute('timeline', [
            'delete' => $quack->getId()
        ]);
    }

}
