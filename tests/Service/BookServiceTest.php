<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookServiceTest extends KernelTestCase
{
    private $bookRepository;
    private $entityManager;
    private $bookService;

    protected function setUp(): void
    {
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bookService = new BookService($this->bookRepository, $this->entityManager);
    }

    private function makeBook(int $id, string $title): Book
    {
        $book = new Book();

        $reflectionClass = new ReflectionClass(Book::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($book, $id);

        $book->setTitle($title);

        return $book;
    }

    public function testGetBookById()
    {
        $book = $this->makeBook(1, 'Test Book');

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($book);

        $result = $this->bookService->getBookById(1);

        $this->assertSame($book, $result);
    }

    public function testGetAll()
    {
        $books = [
            $this->makeBook(1, 'Test Book 1'),
            $this->makeBook(2, 'Test Book 2'),
        ];

        $this->bookRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($books);

        $result = $this->bookService->getAll();

        $this->assertSame($books, $result);
    }

    public function testSaveBook()
    {
        $book = new Book();
        $book->setTitle('New Book');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($book);

        $this->bookService->saveBook($book);
    }

    public function testDeleteBook()
    {
        $book = $this->makeBook(1, '');

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($book);

        $this->bookService->deleteBook($book);
    }
}
