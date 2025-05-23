<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
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

    #[Route('', name: 'test_index')]
    public function index(): Response
    {
        // Create a test product if none exists
        if (count($this->productRepository->findAll()) === 0) {
            $product = new Product();
            $product->setName('Test Product');
            $product->setPrice(19.99);
            $product->setDescription('This is a test product created automatically');
            
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
        
        // Get all products
        $products = $this->productRepository->findAll();
        
        // Build HTML response
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Product Test</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                tr:nth-child(even) { background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h1>Product Test Page</h1>
            <p>This page demonstrates that the Product entity and controller are working correctly.</p>
            
            <h2>Products in Database:</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                </tr>';
        
        foreach ($products as $product) {
            $html .= sprintf(
                '<tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td>$%.2f</td>
                    <td>%s</td>
                </tr>',
                $product->getId(),
                htmlspecialchars($product->getName()),
                $product->getPrice(),
                htmlspecialchars($product->getDescription() ?? '')
            );
        }
        
        $html .= '
            </table>
            
            <h2>API Endpoints:</h2>
            <ul>
                <li>GET /api/products - List all products</li>
                <li>GET /api/products/{id} - Get a specific product</li>
                <li>POST /api/products - Create a new product</li>
                <li>PUT /api/products/{id} - Update a product</li>
                <li>DELETE /api/products/{id} - Delete a product</li>
                <li>GET /api/products/price-range/{min}/{max} - Find products in price range</li>
            </ul>
        </body>
        </html>';
        
        return new Response($html);
    }
}