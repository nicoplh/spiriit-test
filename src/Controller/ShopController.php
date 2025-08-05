<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/')]
    public function indexAction(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAllSorted();

        return $this->render('shop/index.html.twig', [
            "products" => $products,
        ]);
    }

    #[Route('/empty-cart')]
    public function emptyCartAction(): Response
    {
        $this->prepareSession();

        if (\array_key_exists('cart', $_SESSION)) {
            $_SESSION['cart'] = [];
        }

        return $this->redirectToRoute('app_shop_cart');
    }

    #[Route('/cart')]
    public function cartAction(ProductRepository $productRepository): Response
    {
        $this->prepareSession();

        $cart = [];
        $total = 0;

        foreach ($_SESSION['cart'] as $key => $amount) {
            /** @var Product $product */
            $product = $productRepository->findOneBy(['slug' => $key]);
            $cart[$key]['product'] = $product;
            $cart[$key]['amount'] = $amount;

            $total += $product->getPrice() * $amount;
        }

        return $this->render('shop/cart.html.twig', [
            "cart" => $cart,
            "total" => $total,
        ]);
    }

    #[Route('/cart/delete/{slug}')]
    public function deleteFromCartAction(Request $request): Response
    {
        $this->prepareSession();

        $slug = $request->attributes->get('slug');

        unset($_SESSION['cart'][$slug]);

        return $this->redirectToRoute('app_shop_cart');
    }

    #[Route('/product/{slug}')]
    public function showAction(Request $request, ProductRepository $productRepository): Response
    {
        $slug = $request->attributes->get('slug');

        $product = $productRepository->findOneBy([
            'slug' => $slug,
        ]);

        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $amount = $form->getData()['amount'];

            $this->updateCart($slug, $amount);

            return $this->redirectToRoute('app_shop_cart');
        }

        return $this->render('shop/show.html.twig', [
            'form' => $form,
            "product" => $product,
        ]);
    }

    public function cartCountAction(): Response
    {
        $this->prepareSession();

        $count = \array_reduce($_SESSION['cart'], static function ($total, $amount) {
            $total += $amount;

            return $total;
        }, 0);

        return $this->render('shop/_cart_count.html.twig', [
            'count' => $count,
        ]);
    }

    private function updateCart(string $slug, int $amount): void
    {
        $this->prepareSession();

        if (\array_key_exists($slug, $_SESSION['cart'])) {
            $_SESSION['cart'][$slug] += $amount;
        } else {
            $_SESSION['cart'][$slug] = $amount;
        }

        if (0 === $_SESSION['cart'][$slug]) {
            unset($_SESSION['cart'][$slug]);
        }
    }

    private function prepareSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!\array_key_exists('cart', $_SESSION)) {
            $_SESSION['cart'] = [];
        }
    }
}
