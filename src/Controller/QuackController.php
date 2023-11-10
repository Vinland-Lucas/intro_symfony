<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Repository\QuackRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

    #[Route('/feed', name: 'timeline', methods: ['GET'])]
    public function showAllQuacks(EntityManagerInterface $entityManager): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->findAll();

        if (!$quack) {
            throw $this->createNotFoundException(
              'No quacks found here'
            );
        }

        return $this->render('quack/index.html.twig', [
            'controller_name' => 'QuackController',
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

        return $this->render('quack/index.html.twig', [
            'controller_name' => 'QuackController',
        ]);
    }

    #[Route('/quack', name: 'create_quack', methods: ['POST'])]
    public function createQuack(EntityManagerInterface $entityManager, Request $request): Response
    {
        $quack = new Quack();
        $quack->setContent("Yoooooooo ! C'est mon premier QUACK xD !");
        $quack->setCreatedAt('2023-11-10 10:04');

        $form = $this->createFormBuilder($quack)
            ->add('quack', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $quack = $form->getData();

            return $this->redirectToRoute('quack_success');
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($quack);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->render('quack/form.html.twig', [
            'form' => $form,
        ]);

//        return new Response('Saved new quack with id '.$quack->getId());
    }

    #[Route('/quack/edit/{id}', name: 'edit_quack', methods: 'PUT')]
    public function editQuack(EntityManagerInterface $entityManager, int $id): Response
    {
        $quack = $entityManager->getRepository(Quack::class)->find($id);

        if (!$quack) {
            throw $this->createNotFoundException(
                'No quack found for id '.$id
            );
        }

        $quack->setContent("J'ai modifié mon quack !!!");
        $entityManager->flush();

        return $this->redirectToRoute('timeline', [
            'id' => $quack->getId()
        ]);
    }

    #[Route('/quack/delete/{id}', name: 'delete_quack', methods: 'PUT')]
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
            'id' => $quack->getId()
        ]);
    }


}
