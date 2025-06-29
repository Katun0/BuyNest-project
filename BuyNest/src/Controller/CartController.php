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

#[IsGranted('ROLE_USER')]
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
            $cart->setUserID($user->getId());;
            $cart->setCreatedAt(new \DateTimeImmutable());
            $cart->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($cart);
        }


        $existingItem = null;
        foreach ($cart->getItemOnCarts() as $item) {
            if ($item->getProductID()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $itemOnCart = new ItemOnCart();
            $itemOnCart->setQuantity(1);
            $itemOnCart->setPriceAtTime($product->getInventories()->first()->getPrice());
            $itemOnCart->setProductID($product);;
            $itemOnCart->setCartID($cart);
            $itemOnCart->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($itemOnCart);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
