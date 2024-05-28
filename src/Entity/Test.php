<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Tstart = null;

    #[ORM\Column]
    private ?int $Dist = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\OneToMany(targetEntity: Entry::class, mappedBy: 'Test')]
    private Collection $entries;

    #[ORM\Column(nullable: true)]
    private ?int $AnSco = null;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }

    public function getTstart(): ?\DateTimeInterface
    {
        return $this->Tstart;
    }

    public function setTstart(?\DateTimeInterface $Tstart): static
    {
        $this->Tstart = $Tstart;

        return $this;
    }

    public function getDist(): ?int
    {
        return $this->Dist;
    }

    public function setDist(int $Dist): static
    {
        $this->Dist = $Dist;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setTests($this);
        }

        return $this;
    }

    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getTests() === $this) {
                $entry->setTests(null);
            }
        }

        return $this;
    }

    public function getAnSco(): ?int
    {
        return $this->AnSco;
    }

    public function setAnSco(?int $AnSco): static
    {
        $this->AnSco = $AnSco;

        return $this;
    }
}
