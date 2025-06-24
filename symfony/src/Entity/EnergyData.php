<?php

namespace App\Entity;

use App\Repository\EnergyDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnergyDataRepository::class)]
class EnergyData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $consumption = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $production = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $cost = null;

    #[ORM\Column(length: 50)]
    private ?string $source = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $efficiency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getConsumption(): ?float
    {
        return $this->consumption;
    }

    public function setConsumption(float $consumption): static
    {
        $this->consumption = $consumption;
        return $this;
    }

    public function getProduction(): ?float
    {
        return $this->production;
    }

    public function setProduction(float $production): static
    {
        $this->production = $production;
        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
        return $this;
    }

    public function getEfficiency(): ?float
    {
        return $this->efficiency;
    }

    public function setEfficiency(?float $efficiency): static
    {
        $this->efficiency = $efficiency;
        return $this;
    }
} 