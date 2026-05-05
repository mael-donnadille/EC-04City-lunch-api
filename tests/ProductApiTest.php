<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
    public function testCannotCreateProductWithNegativePrice(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Produit invalide',
                'description' => 'Produit avec prix invalide',
                'price' => -5,
                'type' => 'plat'
            ])
        );

        $this->assertResponseStatusCodeSame(400);

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(
            'Le prix doit être supérieur à 0',
            $response['message']
        );
    }
}