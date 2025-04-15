<?php

namespace App\Entity;

use App\Helpers\AppHelper;
use App\Repository\AnneeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeRepository::class)]
class Annee
{

    public function __construct()
    {
        $this->annee_uuid = AppHelper::generateUuid();
        $this->departementHistoriques = new ArrayCollection();
        $this->programmes = new ArrayCollection();
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

    /**
     * @var Collection<int, DepartementHistorique>
     */
    #[ORM\OneToMany(targetEntity: DepartementHistorique::class, mappedBy: 'annee')]
    private Collection $departementHistoriques;

    /**
     * @var Collection<int, Programme>
     */
    #[ORM\OneToMany(targetEntity: Programme::class, mappedBy: 'annee')]
    private Collection $programmes;

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

    /**
     * @return Collection<int, DepartementHistorique>
     */
    public function getDepartementHistoriques(): Collection
    {
        return $this->departementHistoriques;
    }

    public function addDepartementHistorique(DepartementHistorique $departementHistorique): static
    {
        if (!$this->departementHistoriques->contains($departementHistorique)) {
            $this->departementHistoriques->add($departementHistorique);
            $departementHistorique->setAnnee($this);
        }

        return $this;
    }

    public function removeDepartementHistorique(DepartementHistorique $departementHistorique): static
    {
        if ($this->departementHistoriques->removeElement($departementHistorique)) {
            // set the owning side to null (unless already changed)
            if ($departementHistorique->getAnnee() === $this) {
                $departementHistorique->setAnnee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setAnnee($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getAnnee() === $this) {
                $programme->setAnnee(null);
            }
        }

        return $this;
    }
}
