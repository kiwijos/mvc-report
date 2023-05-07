<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/product/create', name: 'create_product', methods: ['GET'])]
    public function createProduct(): Response
    {
        return $this->render('product/create.html.twig');
    }

    #[Route('/product/create', name: 'create_product_post', methods: ['POST'])]
    public function createProductPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var string $name */
        $name = $request->request->get('name', '');

        /** @var int $price */
        $price = intval($request->request->get('price', 0));

        /** @var string $description */
        $description = $request->request->get('description', '');

        /** @var string $company */
        $company = $request->request->get('company', '');

        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setCompany($company);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('product_show_all');
    }

    #[Route('/product/show/', name: 'product_show_all')]
    public function showAll(ProductRepository $productRepository): Response
    {
        $products = $productRepository
            ->findAll();

        return $this->render('product/show_all.html.twig', ['products' => $products]);
    }

    #[Route('/product/show/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit', methods: ['GET'])]
    public function edit(Product $product): Response
    {
        return $this->render('product/edit.html.twig', ['product' => $product]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit_post', methods: ['POST'])]
    public function editPost(Request $request, EntityManagerInterface $entityManager, Product $product): Response
    {

        /** @var string $name */
        $name = $request->request->get('name', $product->getName());

        /** @var int $price */
        $price = intval($request->request->get('price', $product->getPrice()));

        /** @var string $description */
        $description = $request->request->get('description', $product->getDescription());

        /** @var string $company */
        $company = $request->request->get('company', $product->getCompany());

        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setCompany($company);
        $entityManager->flush();

        return $this->redirectToRoute('product_show', [
            'id' => $product->getId()
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete', methods: ['GET'])]
    public function delete(Product $product): Response
    {
        return $this->render('product/delete.html.twig', ['product' => $product]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete_post', methods: ['POST'])]
    public function deletePost(EntityManagerInterface $entityManager, Product $product): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('product_show_all');
    }
}
