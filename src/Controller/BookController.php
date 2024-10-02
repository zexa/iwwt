<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\NewBookFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/books', name: 'app_books')]
    public function books(): Response
    {
        return $this->render('books.html.twig', [
            'books' => [
                [
                    'name' => "Clean Architecture: A Craftsman's Guide to Software Structure and Design 1st Edition",
                    'author' => "Robert Martin",
                    'isbn' => '0134494164',
                    'publication_date' => 'September 10, 2017',
                    'genres' => ['non-fiction'],
                    'num_copies' => 3,
                ]
            ],
        ]);
    }

    #[Route(path: '/book/new', name: 'app_new_book')]
    public function newBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(NewBookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_books');
        }

        return $this->render('new_book.html.twig', ['form' => $form->createView()]);
    }
}
