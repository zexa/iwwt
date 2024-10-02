<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController {
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
}