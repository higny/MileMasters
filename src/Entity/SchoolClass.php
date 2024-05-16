<?php

namespace App\Entity;

use App\Repository\SchoolClassRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolClassRepository::class)]
#[ORM\Table(name: 'CLAS')]
class SchoolClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Niv = null;

    #[ORM\Column(length: 255)]
    private ?string $Ident = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiv(): ?int
    {
        return $this->Niv;
    }

    public function setNiv(int $Niv): static
    {
        $this->Niv = $Niv;

        return $this;
    }

    public function getIdent(): ?string
    {
        return $this->Ident;
    }

    public function setIdent(string $Ident): static
    {
        $this->Ident = $Ident;

        return $this;
    }
}
