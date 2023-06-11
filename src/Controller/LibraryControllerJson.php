<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Main\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LibraryControllerJson extends AbstractController
{
    #[Route("/api/library/", name: "json_library")]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('library/json/index.html.twig', ['books' => $books]);
    }

    #[Route("/api/library/books", name: "json_books")]
    public function showAll(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();

        if (empty($books)) {
            throw $this->createNotFoundException(
                'No books found'
            );
        }

        $response = $this->json($books);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/api/library/book/{isbn}", name: "json_book")]
    public function showByIsbn(Book $book): JsonResponse
    {
        $response = $this->json($book);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }
}
