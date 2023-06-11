<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Main\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'library_index')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig');
    }

    #[Route('/library/create', name: 'library_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('library/create.html.twig');
    }

    #[Route('/library/create', name: 'library_create_post', methods: ['POST'])]
    public function createPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var string $title */
        $title = $request->request->get('title');

        /** @var string $isbn ISBN number (13 digits). */
        $isbn = $request->request->get('isbn');

        /** @var string $description */
        $description = $request->request->get('description');

        /** @var string $author */
        $author = $request->request->get('author');

        /** @var string $imageUrl URL to image used as book cover. */
        $imageUrl = $request->request->get('image_url');

        $book = new Book();
        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setDescription($description);
        $book->setAuthor($author);
        $book->setImageUrl($imageUrl);

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->redirectToRoute('library_read_many');
    }

    #[Route('/library/read/', name: 'library_read_many')]
    public function readMany(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('library/read_many.html.twig', ['books' => $books]);
    }

    #[Route('/library/read/{id}', name: 'library_read_one')]
    public function readById(Book $book): Response
    {
        return $this->render('library/read.html.twig', ['book' => $book]);
    }

    #[Route('/library/edit/{id}', name: 'library_edit', methods: ['GET'])]
    public function edit(Book $book): Response
    {
        return $this->render('library/edit.html.twig', ['book' => $book]);
    }

    #[Route('/library/edit/{id}', name: 'library_edit_post', methods: ['POST'])]
    public function editPost(Request $request, EntityManagerInterface $entityManager, Book $book): Response
    {
        /** @var string $title */
        $title = $request->request->get('title', $book->getTitle());

        /** @var string $isbn ISBN number (13 digits). */
        $isbn = $request->request->get('isbn', $book->getIsbn());

        /** @var string $description */
        $description = $request->request->get('description', $book->getDescription());

        /** @var string $author */
        $author = $request->request->get('author', $book->getAuthor());

        /** @var string $imageUrl URL to image used as book cover. */
        $imageUrl = $request->request->get('image_url', $book->getImageUrl());

        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setDescription($description);
        $book->setAuthor($author);
        $book->setImageUrl($imageUrl);
        $entityManager->flush();

        return $this->redirectToRoute('library_read_one', [
            'id' => $book->getId()
        ]);
    }

    #[Route('/library/delete/{id}', name: 'library_delete', methods: ['GET'])]
    public function delete(Book $book): Response
    {
        return $this->render('library/delete.html.twig', ['book' => $book]);
    }

    #[Route('/library/delete/{id}', name: 'library_delete_post', methods: ['POST'])]
    public function deletePost(EntityManagerInterface $entityManager, Book $book): Response
    {
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('library_read_many');
    }

    #[Route('/library/reset/', name: 'library_reset', methods: ['POST'])]
    public function reset(BookRepository $bookRepository): Response
    {
        $bookRepository->drop();   // Drop book table
        $bookRepository->create(); // Create book table
        $bookRepository->insert(); // Insert default rows

        return $this->redirectToRoute('library_read_many');
    }
}
