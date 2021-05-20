<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ShipOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShipOrderRepository::class)
 */
class ShipOrder
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    private int $orderid;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="shipOrders")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $orderperson;

    /**
     * @ORM\Column(type="array")
     *
     * @var array<string>
     */
    private array $items = [];

    /**
     * @ORM\Column(type="json")
     *
     * @var array<string>
     */
    private array $shipto = [];

    public function __construct()
    {
        $this->items_ = new ArrayCollection();
    }

    public function getOrderId(): ?int
    {
        return $this->orderid;
    }

    public function getOrderPerson(): ?Person
    {
        return $this->orderperson;
    }

    /**
     * @return array<string>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<string>
     */
    public function getShipto(): array
    {
        return $this->shipto;
    }

    /**
     * @param array<string> $shipTo
     * @param array<string> $items
     */
    public function populateShipOrder(
        Person $orderPerson,
        array $shipTo,
        array $items
    ): void {
        $this->orderperson = $orderPerson;
        $this->shipto = $shipTo;
        $this->items = $items;
    }
}
