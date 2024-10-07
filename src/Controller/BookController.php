<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly BookService $bookService,
    ) {
    }

    #[Route(path: '/', name: 'app_index')]
    public function getIndex(): Response
    {
        $books = $this->bookService->getAll();

        $newBookForm = $form = $this->createForm(BookFormType::class);

        return $this->render('pages/books.html.twig', [
            'books' => $books,
            'new_book_form' => $newBookForm,
        ]);
    }

    #[Route(path: '/books', name: 'app_books')]
    public function getBooks(): Response
    {
        $books = $this->bookService->getAll();

        return $this->render('components/books.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route(path: '/book', name: 'app_new_book')]
    public function newBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        $this->logger->debug('new book', [
            'form_errors' => $form->getErrors(true, false),
            'is_submitted_and_valid' => $form->isSubmitted() && $form->isValid(),
            'book' => $book,
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->saveBook($book);
            $this->entityManager->flush();
            return new Response('OK', Response::HTTP_OK);
        }

        return $this->render('components/book_form.html.twig', [
            'form' => $form->createView(), 
            'is_editing' => false,
        ]);
    }

    // PUT/PATCH methods arent used here because client side forms do not support other methods
    // without method overloading
    #[Route(path: '/book/{bookId}/edit', name: 'app_edit_book')]
    public function editBook(Request $request, int $bookId): Response
    {
        $book = $this->bookService->getBookById($bookId);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        $this->logger->debug('Editing book', [
            'book' => $book,
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->saveBook($book);
            $this->entityManager->flush();

            return new Response('OK', Response::HTTP_OK);
        }

        return $this->render('components/book_form.html.twig', [
            'form' => $form->createView(), 
            'is_editing' => true,
        ]);
    }

    // DELETE method isnt used here because client side forms do not support other methods
    // without method overloading
    #[Route(path: '/book/{bookId}/delete', name: 'app_delete_book')]
    public function deleteBook(int $bookId): Response
    {
        $book = $this->bookService->getBookById($bookId);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $this->bookService->deleteBook($book);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }
}
