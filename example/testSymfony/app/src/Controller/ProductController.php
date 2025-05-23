<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    #[Route('', name: 'product_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
        ];
        
        return $this->json($data);
    }

    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['price'])) {
            return $this->json(['error' => 'Name and price are required'], Response::HTTP_BAD_REQUEST);
        }
        
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        
        $this->entityManager->flush();
        
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/price-range/{min}/{max}', name: 'product_price_range', methods: ['GET'])]
    public function findByPriceRange(float $min, float $max): JsonResponse
    {
        $products = $this->productRepository->findByPriceRange($min, $max);
        
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }
        
        return $this->json($data);
    }
}