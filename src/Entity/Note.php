<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $note_1 = null;

    #[ORM\Column]
    private ?float $note_2 = null;

    #[ORM\Column]
    private ?float $note_3 = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $student = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $max_delay = null;

    #[ORM\Column(nullable: true)]
    private ?float $moyenne = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->max_delay = null;
        $this->note_1 = 0;
        $this->note_2 = 0;
        $this->note_3 = 0;
        $this->calculerMoyenne();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
        $this->calculerMoyenne();
    }

    private function calculerMoyenne(): void
    {
        if ($this->note_1 !== null && $this->note_2 !== null && $this->note_3 !== null) {
            $this->moyenne = ($this->note_1 * 0.3) + ($this->note_2 * 0.3) + ($this->note_3 * 0.4);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote1(): ?float
    {
        return $this->note_1;
    }

    public function setNote1(float $note_1): static
    {
        $this->note_1 = $note_1;
        $this->calculerMoyenne();
        return $this;
    }

    public function getNote2(): ?float
    {
        return $this->note_2;
    }

    public function setNote2(float $note_2): static
    {
        $this->note_2 = $note_2;
        $this->calculerMoyenne();
        return $this;
    }

    public function getNote3(): ?float
    {
        return $this->note_3;
    }

    public function setNote3(float $note_3): static
    {
        $this->note_3 = $note_3;
        $this->calculerMoyenne();
        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getStudent(): ?Etudiant
    {
        return $this->student;
    }

    public function setStudent(?Etudiant $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getMaxDelay(): ?\DateTimeInterface
    {
        return $this->max_delay;
    }

    public function setMaxDelay(?\DateTimeInterface $max_delay): static
    {
        $this->max_delay = $max_delay;

        return $this;
    }

    public function getMoyenne(): ?float
    {
        return $this->moyenne;
    }

    public function setMoyenne(?float $moyenne): static
    {
        $this->moyenne = $moyenne;

        return $this;
    }
}