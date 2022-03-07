<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RequestRepository::class)
 */
#[ApiResource]
class Request
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Line::class, mappedBy="request", orphanRemoval=true, cascade={"persist"})
     */
    private $fields;

    /**
     * @ORM\Column(type="integer")
     */
    private $newVariantId;

    /**
     * @ORM\Column(type="integer")
     */
    private $oldVariantId;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Line>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Line $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setRequest($this);
        }

        return $this;
    }

    public function removeField(Line $field): self
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getRequest() === $this) {
                $field->setRequest(null);
            }
        }

        return $this;
    }

    public function getNewVariantId(): ?int
    {
        return $this->newVariantId;
    }

    public function setNewVariantId(int $newVariantId): self
    {
        $this->newVariantId = $newVariantId;

        return $this;
    }

    public function getOldVariantId(): ?int
    {
        return $this->oldVariantId;
    }

    public function setOldVariantId(int $oldVariantId): self
    {
        $this->oldVariantId = $oldVariantId;

        return $this;
    }
}
