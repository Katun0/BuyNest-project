<?php

namespace App\Entity;

use App\Repository\ItemOnCartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemOnCartRepository::class)]
class ItemOnCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'itemOnCarts')]
    private ?ShoppingCart $cartID = null;

    #[ORM\ManyToOne(inversedBy: 'itemOnCarts')]
    private ?product $productID = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $priceAtTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCartID(): ?ShoppingCart
    {
        return $this->cartID;
    }

    public function setCartID(?ShoppingCart $cartID): static
    {
        $this->cartID = $cartID;

        return $this;
    }

    public function getProductID(): ?product
    {
        return $this->productID;
    }

    public function setProductID(?product $productID): static
    {
        $this->productID = $productID;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPriceAtTime(): ?float
    {
        return $this->priceAtTime;
    }

    public function setPriceAtTime(float $priceAtTime): static
    {
        $this->priceAtTime = $priceAtTime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
