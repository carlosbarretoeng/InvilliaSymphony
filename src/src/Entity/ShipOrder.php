<?php

namespace App\Entity;

use App\Repository\ShipOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShipOrderRepository::class)
 */
class ShipOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $orderid;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="shipOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderperson;

    /**
     * @ORM\Column(type="array")
     */
    private $items = [];

    /**
     * @ORM\Column(type="json")
     */
    private $shipto = [];

    public function getOrderId(): ?int
    {
        return $this->orderid;
    }

    public function getOrderPerson(): ?Person
    {
        return $this->orderperson;
    }

    public function setOrderPerson(?Person $orderperson): self
    {
        $this->orderperson = $orderperson;

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getShipto(): ?array
    {
        return $this->shipto;
    }

    public function setShipto(array $shipto): self
    {
        $this->shipto = $shipto;

        return $this;
    }
}
