<?php

namespace App\Controller;

use App\Entity\ItemOnCart;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Repository\ShoppingCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET', 'POST'])]
    public function addToCart(
        Security $security,
        ShoppingCartRepository $shoppingCartRepository,
        EntityManagerInterface $entityManager,
        Product $product
    ): Response {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $shoppingCartRepository->findOneBy(['userID' => $user]);

        if (!$cart) {
            $cart = new ShoppingCart();
            $cart->setUserID($user);
            $cart->setCreatedAt(new \DateTimeImmutable());
            $cart->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($cart);
        }



        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/add/item/{id}', name: 'app_cart_add_item', methods: ['GET', 'POST'])]
    public function addToCartItem(
        ItemOnCart $itemOnCart,
        Product $product,
        ShoppingCartRepository $shoppingCartRepository,
        EntityManagerInterface $entityManager,
    ): Response {

    }
}
