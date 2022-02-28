<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\StatusRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class Status
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="status", orphanRemoval=true)
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="status", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
