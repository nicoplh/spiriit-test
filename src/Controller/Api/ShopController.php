<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ShopController extends AbstractController
{
    #[Route('/api/')]
    public function indexAction(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAllSorted();

        return JsonResponse::fromJsonString($serializer->serialize($products, 'json'));
    }
}
