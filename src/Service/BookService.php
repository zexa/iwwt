<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService {
    public function __construct(
        private readonly BookRepository $bookRepository, 
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getBookById(int $id): Book {
        return $this->bookRepository->find($id);
    }

    public function getAll(): array {
        return $this->bookRepository->findAll();
    }

    public function saveBook(Book $book): void {
        $this->entityManager->persist($book);
    }

    public function deleteBook(Book $book): void {
        $this->entityManager->remove($book);
    }
}