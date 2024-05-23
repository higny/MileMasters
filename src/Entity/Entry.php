<?php

namespace App\Entity;

use App\Repository\EntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $NoDos = null;

    #[ORM\Column]
    private ?bool $Rw = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $Tstart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Tend = null;

    #[ORM\Column(nullable: true)]
    private ?\DateInterval $Temps = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Test $Test = null;

    #[ORM\ManyToOne(inversedBy: 'Entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoDos(): ?int
    {
        return $this->NoDos;
    }

    public function setNoDos(int $NoDos): static
    {
        $this->NoDos = $NoDos;

        return $this;
    }

    public function isRw(): ?bool
    {
        return $this->Rw;
    }

    public function setRw(bool $Rw): static
    {
        $this->Rw = $Rw;

        return $this;
    }

    public function getTstart(): ?\DateTimeInterface
    {
        return $this->Tstart;
    }

    public function setTstart(\DateTimeInterface $Tstart): static
    {
        $this->Tstart = $Tstart;

        return $this;
    }

    public function getTend(): ?\DateTimeInterface
    {
        return $this->Tend;
    }

    public function setTend(?\DateTimeInterface $Tend): static
    {
        $this->Tend = $Tend;

        return $this;
    }

    public function getTemps(): ?\DateInterval
    {
        return $this->Temps;
    }

    public function setTemps(?\DateInterval $Temps): static
    {
        $this->Temps = $Temps;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->Test;
    }

    public function setTest(?Test $Test): static
    {
        $this->Test = $Test;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }
}
