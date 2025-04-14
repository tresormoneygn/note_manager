<?php

namespace App\Entity;

use App\Helpers\AppHelper;
use App\Repository\AnneeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeRepository::class)]
class Annee
{

    public function __construct()
    {
        $this->annee_uuid = AppHelper::generateUuid();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $is_progress = null;

    #[ORM\Column(length: 255)]
    private ?string $annee_uuid = null;

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

    public function isProgress(): ?bool
    {
        return $this->is_progress;
    }

    public function setIsProgress(bool $is_progress): static
    {
        $this->is_progress = $is_progress;

        return $this;
    }

    public function getAnneeUuid(): ?string
    {
        return $this->annee_uuid;
    }

    public function setAnneeUuid(string $annee_uuid): static
    {
        $this->annee_uuid = $annee_uuid;

        return $this;
    }
}
