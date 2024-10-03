<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/', name: 'app_books')]
    public function books(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->render('books.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route(methods: ['GET', 'POST'], path: '/book', name: 'app_new_book')]
    public function newBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_books');
        }

        return $this->render('book.html.twig', [
            'form' => $form->createView(), 
            'is_editing' => false,
        ]);
    }

    // Note: not very restful as we're not using methods like put or patch, but there seems
    // to be issues when submitting forms with other methods.
    #[Route(methods: ['GET', 'POST'], path: '/book/{bookId}', name: 'app_edit_book')]
    public function editBook(Request $request): Response
    {
        $bookId = $request->attributes->get('bookId');
        $book = $this->bookRepository->find($bookId);
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        $this->logger->debug('editing book', [
            'is_submitted' => $form->isSubmitted(),
            'form_errors' => $form->getErrors(true, false),
            'book' => $book,
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_books');
        }

        return $this->render('book.html.twig', [
            'form' => $form->createView(), 
            'is_editing' => true,
        ]);
    }

    #[Route(methods: ['DELETE'], path: '/book/{bookId}', name: 'app_delete_book')]
    public function deleteBook(Request $request): Response
    {
        $bookId = $request->attributes->get('bookId');
        $book = $this->bookRepository->find($bookId);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_books');
    }
}
