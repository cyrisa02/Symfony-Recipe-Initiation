<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ingredient.
 *
 * @ORM\Table("ingredient")
 * @ORM\Entity(repositoryClass= IngredientRepository::class)
 * @UniqueEntity("name")
 */
class Ingredient
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min= 2, max= 50)
     */
    protected string $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     * @Assert\LessThan(200)
     */
    private float $price;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotNull
     */
    private ?DateTimeImmutable $createdAt;

    /**
     * Constructor for the date.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    //Nécessaire pour que dans RecipeTYpe ->add('ingredients'), RecipeType reconnaisse que ingredients est un string

    public function __toString()
    {
        return $this->name;
    }
}
