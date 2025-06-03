<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $acronym = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $last_modified = null;

    /**
     * @var Collection<int, FederalUnit>
     */
    #[ORM\OneToMany(targetEntity: FederalUnit::class, mappedBy: 'country_id')]
    private Collection $federalUnits;

    public function __construct()
    {
        $this->federalUnits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): static
    {
        $this->acronym = $acronym;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->last_modified;
    }

    public function setLastModified(?\DateTimeImmutable $last_modified): static
    {
        $this->last_modified = $last_modified;

        return $this;
    }

    /**
     * @return Collection<int, FederalUnit>
     */
    public function getFederalUnits(): Collection
    {
        return $this->federalUnits;
    }

    public function addFederalUnit(FederalUnit $federalUnit): static
    {
        if (!$this->federalUnits->contains($federalUnit)) {
            $this->federalUnits->add($federalUnit);
            $federalUnit->setCountryId($this);
        }

        return $this;
    }

    public function removeFederalUnit(FederalUnit $federalUnit): static
    {
        if ($this->federalUnits->removeElement($federalUnit)) {
            // set the owning side to null (unless already changed)
            if ($federalUnit->getCountryId() === $this) {
                $federalUnit->setCountryId(null);
            }
        }

        return $this;
    }
}
