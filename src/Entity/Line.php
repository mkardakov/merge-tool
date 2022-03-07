<?php

namespace App\Entity;

use App\Repository\LineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LineRepository::class)
 */
class Line
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $variantPropertyName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $side;

    /**
     * @ORM\ManyToOne(targetEntity=Request::class, inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $request;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVariantPropertyName(): ?string
    {
        return $this->variantPropertyName;
    }

    public function setVariantPropertyName(string $variantPropertyName): self
    {
        $this->variantPropertyName = $variantPropertyName;

        return $this;
    }

    public function getSide(): ?bool
    {
        return $this->side;
    }

    public function setSide(bool $side): self
    {
        $this->side = $side;

        return $this;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(?Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}
