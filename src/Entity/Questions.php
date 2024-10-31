<?php

namespace App\Entity;

use App\Repository\QuestionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionsRepository::class)]
class Questions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    private ?string $Reponse = null;

    #[ORM\Column]
    private ?int $Nombre = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->Reponse;
    }

    public function setReponse(string $Reponse): static
    {
        $this->Reponse = $Reponse;

        return $this;
    }
    public function getNombre(): ?int
    {
        return $this->Nombre;
    }

    public function setNombre(int $Nombre): static
    {
        $this->Nombre = $Nombre;

        return $this;
    }
}
