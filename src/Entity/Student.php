<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Prenom = null;

    #[ORM\Column]
    private ?bool $Sexe = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolClass $Class = null;

    #[ORM\OneToMany(targetEntity: Entry::class, mappedBy: 'student')]
    private Collection $Entries;

    public function __construct()
    {
        $this->Entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function isSexe(): ?bool
    {
        return $this->Sexe;
    }

    public function setSexe(bool $Sexe): static
    {
        $this->Sexe = $Sexe;

        return $this;
    }

    public function getClass(): ?SchoolClass
    {
        return $this->Class;
    }

    public function setClass(?SchoolClass $Class): static
    {
        $this->Class = $Class;

        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->Entries;
    }

    public function addEntry(Entry $entry): static
    {
        if (!$this->Entries->contains($entry)) {
            $this->Entries->add($entry);
            $entry->setStudent($this);
        }

        return $this;
    }

    public function removeEntry(Entry $entry): static
    {
        if ($this->Entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getStudent() === $this) {
                $entry->setStudent(null);
            }
        }

        return $this;
    }
}
