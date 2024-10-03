<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\NewBookFormType;
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

    #[Route(path: '/books', name: 'app_books')]
    public function books(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->render('books.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route(path: '/book/new', name: 'app_new_book')]
    public function newBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(NewBookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->debug('submitting book', [
                'book' => var_export($book, true),
                'form' => $form->getData(),
                'request' => $request->request->all(),
            ]);
            $this->entityManager->persist($book);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_books');
        }

        return $this->render('new_book.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/book/{bookId}/new', name: 'app_delete_book')]
    public function deleteBook(Request $request): Response
    {
        $bookId = $request->attributes->get('bookId');
        $book = $this->bookRepository->find($bookId);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_books');
    }
}
