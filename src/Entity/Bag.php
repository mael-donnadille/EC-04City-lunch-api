<?php

namespace App\Entity;

use App\Repository\BagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BagRepository::class)]
class Bag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'bag', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeliveryPerson $deliveryPerson = null;

    /**
     * @var Collection<int, BagItem>
     */
    #[ORM\OneToMany(targetEntity: BagItem::class, mappedBy: 'bag', orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryPerson(): ?DeliveryPerson
    {
        return $this->deliveryPerson;
    }

    public function setDeliveryPerson(DeliveryPerson $deliveryPerson): static
    {
        $this->deliveryPerson = $deliveryPerson;

        return $this;
    }

    /**
     * @return Collection<int, BagItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(BagItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setBag($this);
        }

        return $this;
    }

    public function removeItem(BagItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBag() === $this) {
                $item->setBag(null);
            }
        }

        return $this;
    }
}
