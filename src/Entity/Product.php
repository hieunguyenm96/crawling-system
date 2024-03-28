<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(columns: ['lz_id'], name: 'lz_id_idx')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $lz_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $original_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price = null;

    #[ORM\Column(length: 10)]
    private ?string $discount = null;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getLzId() : ?string
    {
        return $this->lz_id;
    }

    public function setLzId(string $lz_id) : static
    {
        $this->lz_id = $lz_id;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(string $name) : static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }

    public function setImage(string $image) : static
    {
        $this->image = $image;

        return $this;
    }

    public function getOriginalPrice() : ?string
    {
        return $this->original_price;
    }

    public function setOriginalPrice(string $original_price) : static
    {
        $this->original_price = $original_price;

        return $this;
    }

    public function getPrice() : ?string
    {
        return $this->price;
    }

    public function setPrice(string $price) : static
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscount() : ?string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount) : static
    {
        $this->discount = $discount;

        return $this;
    }
}
