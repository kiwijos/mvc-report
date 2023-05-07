<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Book;
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
        $book = new Book();
        $book->setTitle($request->request->get('title'));
        $book->setIsbn($request->request->get('isbn'));
        $book->setDescription($request->request->get('description'));
        $book->setAuthor($request->request->get('author'));
        $book->setImageUrl($request->request->get('image_url'));

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

        $book->setTitle($request->request->get('title', $book->getTitle()));
        $book->setIsbn($request->request->get('isbn', $book->getIsbn()));
        $book->setDescription($request->request->get('description', $book->getDescription()));
        $book->setAuthor($request->request->get('author', $book->getAuthor()));
        $book->setImageUrl($request->request->get('image_url', $book->getImageUrl()));
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
