<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="array")
     *
     * @var array<string> $phones
     */
    private array $phones = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array<string>
     */
    public function getPhones(): array
    {
        return $this->phones;
    }

    /**
     * @param array<string> $phones
     */
    public function populate(
        int $personId,
        string $personName,
        array $phones
    ): void {
        $this->id = $personId;
        $this->name = $personName;
        $this->phones = $phones;
    }
}
