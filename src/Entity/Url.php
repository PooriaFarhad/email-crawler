<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UrlRepository::class)
 */
class Url
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Request::class, inversedBy="requestUrls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $request;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reference_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $crawled_at;

    /**
     * @ORM\OneToMany(targetEntity=Email::class, mappedBy="url", orphanRemoval=true)
     */
    private $emails;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(?Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->reference_id;
    }

    public function setReferenceId(?int $reference_id): self
    {
        $this->reference_id = $reference_id;

        return $this;
    }

    public function getCrawledAt(): ?\DateTimeInterface
    {
        return $this->crawled_at;
    }

    public function setCrawledAt(?\DateTimeInterface $crawled_at): self
    {
        $this->crawled_at = $crawled_at;

        return $this;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }
}
