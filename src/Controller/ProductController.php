<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    #[Route('', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = $this->formatProduct($product);
        }

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(?Product $product): JsonResponse
    {
        if (!$product) {
            return $this->json([
                'message' => 'Produit introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->formatProduct($product), JsonResponse::HTTP_OK);
    }

    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'message' => 'JSON invalide'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (
            empty($data['name']) ||
            !isset($data['price']) ||
            empty($data['type'])
        ) {
            return $this->json([
                'message' => 'Les champs name, price et type sont obligatoires'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!in_array($data['type'], ['plat', 'dessert'])) {
            return $this->json([
                'message' => 'Le type doit être plat ou dessert'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($data['price'] <= 0) {
            return $this->json([
                'message' => 'Le prix doit être supérieur à 0'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description'] ?? null);
        $product->setPrice($data['price']);
        $product->setType($data['type']);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json([
            'message' => 'Produit créé avec succès',
            'product' => $this->formatProduct($product)
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(?Product $product, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$product) {
            return $this->json([
                'message' => 'Produit introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'message' => 'JSON invalide'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }

        if (array_key_exists('description', $data)) {
            $product->setDescription($data['description']);
        }

        if (isset($data['price'])) {
            if ($data['price'] <= 0) {
                return $this->json([
                    'message' => 'Le prix doit être supérieur à 0'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $product->setPrice($data['price']);
        }

        if (isset($data['type'])) {
            if (!in_array($data['type'], ['plat', 'dessert'])) {
                return $this->json([
                    'message' => 'Le type doit être plat ou dessert'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $product->setType($data['type']);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Produit modifié avec succès',
            'product' => $this->formatProduct($product)
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(?Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$product) {
            return $this->json([
                'message' => 'Produit introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json([
            'message' => 'Produit supprimé avec succès'
        ], JsonResponse::HTTP_OK);
    }

    private function formatProduct(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'type' => $product->getType(),
        ];
    }
}