<?php

namespace App\Controller;

use App\Entity\Bag;
use App\Entity\DeliveryPerson;
use App\Repository\DeliveryPersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/delivery-persons')]
class DeliveryPersonController extends AbstractController
{
    #[Route('', name: 'delivery_person_index', methods: ['GET'])]
    public function index(DeliveryPersonRepository $deliveryPersonRepository): JsonResponse
    {
        $deliveryPersons = $deliveryPersonRepository->findAll();

        $data = [];

        foreach ($deliveryPersons as $deliveryPerson) {
            $data[] = $this->formatDeliveryPerson($deliveryPerson);
        }

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delivery_person_show', methods: ['GET'])]
    public function show(?DeliveryPerson $deliveryPerson): JsonResponse
    {
        if (!$deliveryPerson) {
            return $this->json([
                'message' => 'Livreur introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($this->formatDeliveryPerson($deliveryPerson), JsonResponse::HTTP_OK);
    }

    #[Route('', name: 'delivery_person_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'message' => 'JSON invalide'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (
            empty($data['firstname']) ||
            empty($data['lastname']) ||
            empty($data['email'])
        ) {
            return $this->json([
                'message' => 'Les champs firstname, lastname et email sont obligatoires'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $plainPassword = bin2hex(random_bytes(4));

        $deliveryPerson = new DeliveryPerson();
        $deliveryPerson->setFirstname($data['firstname']);
        $deliveryPerson->setLastname($data['lastname']);
        $deliveryPerson->setEmail($data['email']);
        $deliveryPerson->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));
        $deliveryPerson->setIsAvailable($data['isAvailable'] ?? true);

        $bag = new Bag();
        $bag->setDeliveryPerson($deliveryPerson);

        $entityManager->persist($deliveryPerson);
        $entityManager->persist($bag);
        $entityManager->flush();

        return $this->json([
            'message' => 'Livreur créé avec succès',
            'temporaryPassword' => $plainPassword,
            'deliveryPerson' => $this->formatDeliveryPerson($deliveryPerson)
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delivery_person_update', methods: ['PUT'])]
    public function update(?DeliveryPerson $deliveryPerson, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$deliveryPerson) {
            return $this->json([
                'message' => 'Livreur introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'message' => 'JSON invalide'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (isset($data['firstname'])) {
            $deliveryPerson->setFirstname($data['firstname']);
        }

        if (isset($data['lastname'])) {
            $deliveryPerson->setLastname($data['lastname']);
        }

        if (isset($data['email'])) {
            $deliveryPerson->setEmail($data['email']);
        }

        if (isset($data['isAvailable'])) {
            $deliveryPerson->setIsAvailable((bool) $data['isAvailable']);
        }

        if (!empty($data['password'])) {
            $deliveryPerson->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Livreur modifié avec succès',
            'deliveryPerson' => $this->formatDeliveryPerson($deliveryPerson)
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'delivery_person_delete', methods: ['DELETE'])]
    public function delete(?DeliveryPerson $deliveryPerson, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$deliveryPerson) {
            return $this->json([
                'message' => 'Livreur introuvable'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($deliveryPerson);
        $entityManager->flush();

        return $this->json([
            'message' => 'Livreur supprimé avec succès'
        ], JsonResponse::HTTP_OK);
    }

    private function formatDeliveryPerson(DeliveryPerson $deliveryPerson): array
    {
        return [
            'id' => $deliveryPerson->getId(),
            'firstname' => $deliveryPerson->getFirstname(),
            'lastname' => $deliveryPerson->getLastname(),
            'email' => $deliveryPerson->getEmail(),
            'isAvailable' => $deliveryPerson->isAvailable(),
        ];
    }
}