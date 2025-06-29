<?php

namespace App\Entity;

use App\Repository\ShoppingCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingCartRepository::class)]
class ShoppingCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sessionID = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, ItemOnCart>
     */
    #[ORM\OneToMany(targetEntity: ItemOnCart::class, mappedBy: 'cartID')]
    private Collection $itemOnCarts;

    public function __construct()
    {
        $this->itemOnCarts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(User $userID): static
    {
        $this->userID = $userID;

        return $this;
    }

    public function getSessionID(): ?string
    {
        return $this->sessionID;
    }

    public function setSessionID(?string $sessionID): static
    {
        $this->sessionID = $sessionID;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, ItemOnCart>
     */
    public function getItemOnCarts(): Collection
    {
        return $this->itemOnCarts;
    }

    public function addItemOnCart(ItemOnCart $itemOnCart): static
    {
        if (!$this->itemOnCarts->contains($itemOnCart)) {
            $this->itemOnCarts->add($itemOnCart);
            $itemOnCart->setCartID($this);
        }

        return $this;
    }

    public function removeItemOnCart(ItemOnCart $itemOnCart): static
    {
        if ($this->itemOnCarts->removeElement($itemOnCart)) {
            // set the owning side to null (unless already changed)
            if ($itemOnCart->getCartID() === $this) {
                $itemOnCart->setCartID(null);
            }
        }

        return $this;
    }
}
