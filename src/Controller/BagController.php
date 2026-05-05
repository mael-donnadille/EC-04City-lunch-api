<?php

namespace App\Controller;

use App\Entity\BagItem;
use App\Entity\DeliveryPerson;
use App\Repository\BagItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/bag')]
class BagController extends AbstractController
{
    #[Route('', name: 'bag_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $deliveryPerson = $this->getUser();

        if (!$deliveryPerson instanceof DeliveryPerson) {
            return $this->json([
                'message' => 'Utilisateur non authentifié'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $bag = $deliveryPerson->getBag();

        if (!$bag) {
            return $this->json([
                'message' => 'Aucun sac trouvé pour ce livreur'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $items = [];

        foreach ($bag->getItems() as $item) {
            $items[] = [
                'product' => [
                    'id' => $item->getProduct()->getId(),
                    'name' => $item->getProduct()->getName(),
                    'type' => $item->getProduct()->getType(),
                    'price' => $item->getProduct()->getPrice(),
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        return $this->json([
            'deliveryPerson' => [
                'id' => $deliveryPerson->getId(),
                'firstname' => $deliveryPerson->getFirstname(),
                'lastname' => $deliveryPerson->getLastname(),
                'email' => $deliveryPerson->getEmail(),
            ],
            'items' => $items
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/items', name: 'bag_add_item', methods: ['POST'])]
    public function addItem(
        Request $request,
        ProductRepository $productRepository,
        BagItemRepository $bagItemRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $deliveryPerson = $this->getUser();

        if (!$deliveryPerson instanceof DeliveryPerson) {
            return $this->json([
                'message' => 'Utilisateur non authentifié'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $bag = $deliveryPerson->getBag();

        if (!$bag) {
            return $this->json([
                'message' => 'Aucun sac trouvé pour ce livreur'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'message' => 'JSON invalide'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (empty($data['productId']) || empty($data['quantity'])) {
            return $this->json([
                'message' => 'Les champs productId et quantity sont obligatoires'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($data['quantity'] <= 0) {
            return $this->json([
                'message' => 'La quantité doit être supérieure à 0'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = $productRepository->find($data['productId']);

        if (!$product) {
            return $this->json([
                'message' => 'Produit introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $existingItem = $bagItemRepository->findOneBy([
            'bag' => $bag,
            'product' => $product
        ]);

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + $data['quantity']);
        } else {
            $bagItem = new BagItem();
            $bagItem->setBag($bag);
            $bagItem->setProduct($product);
            $bagItem->setQuantity($data['quantity']);

            $entityManager->persist($bagItem);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Produit ajouté au sac avec succès'
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/items/{productId}', name: 'bag_remove_item', methods: ['DELETE'])]
    public function removeItem(
        int $productId,
        ProductRepository $productRepository,
        BagItemRepository $bagItemRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $deliveryPerson = $this->getUser();

        if (!$deliveryPerson instanceof DeliveryPerson) {
            return $this->json([
                'message' => 'Utilisateur non authentifie'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $bag = $deliveryPerson->getBag();

        if (!$bag) {
            return $this->json([
                'message' => 'Aucun sac trouvé pour ce livreur'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $product = $productRepository->find($productId);

        if (!$product) {
            return $this->json([
                'message' => 'Produit introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $bagItem = $bagItemRepository->findOneBy([
            'bag' => $bag,
            'product' => $product
        ]);

        if (!$bagItem) {
            return $this->json([
                'message' => 'Ce produit n est pas présent dans le sac'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($bagItem);
        $entityManager->flush();

        return $this->json([
            'message' => 'Produit retire du sac avec succes'
        ], JsonResponse::HTTP_OK);
    }
}