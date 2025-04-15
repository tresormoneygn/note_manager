<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    /**
     * @var Collection<int, DepartementHistorique>
     */
    #[ORM\OneToMany(targetEntity: DepartementHistorique::class, mappedBy: 'departement')]
    private Collection $departementHistoriques;

    public function __construct()
    {
        $this->departementHistoriques = new ArrayCollection();
    }

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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
            $departementHistorique->setDepartement($this);
        }

        return $this;
    }

    public function removeDepartementHistorique(DepartementHistorique $departementHistorique): static
    {
        if ($this->departementHistoriques->removeElement($departementHistorique)) {
            // set the owning side to null (unless already changed)
            if ($departementHistorique->getDepartement() === $this) {
                $departementHistorique->setDepartement(null);
            }
        }

        return $this;
    }
}
