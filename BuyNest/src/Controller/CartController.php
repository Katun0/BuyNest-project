<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\ItemOnCart;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Repository\InventoryRepository;
use App\Repository\ItemOnCartRepository;
use App\Repository\ShoppingCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart', name: 'app_cart')]
    public function renderTemplate(
        Security $security,
        ShoppingCartRepository $shoppingCartRepository,
        EntityManagerInterface $em){
        $user = $security->getUser();

        $cart = $shoppingCartRepository->findOneByUser($user);

        if (!$cart) {
            $cart = new ShoppingCart();
            $cart->setUserID($user);
            $cart->setCreatedAt(new \DateTimeImmutable());

            $em->persist($cart);
            $em->flush();
        }

        $items = $cart->getItemOnCarts();
        $inventory = new Inventory();

        $total = 0;
        foreach ($items as $item) {
            $total += $item->getPriceAtTime() * $item->getQuantity();
        }

        return $this->render('cart/cart.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
}

    #[Route('/cart/increase/{id}', name: 'cart_increase')]
    public function increaseQuantity(ItemOnCart $item, EntityManagerInterface $em): RedirectResponse
    {
        $item->setQuantity($item->getQuantity() + 1);
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
    public function decreaseQuantity(ItemOnCart $item, EntityManagerInterface $em): RedirectResponse
    {
        if ($item->getQuantity() > 1) {
            $item->setQuantity($item->getQuantity() - 1);
            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }
}
