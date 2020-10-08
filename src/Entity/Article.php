<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reprezentuje záznamy databázové tabulky článků v redakčním systému.
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @UniqueEntity("url", message="Článek s touto URL adresou již existuje!")
 * @package App\Entity
 */
class Article
{
    /**
     * @var int Unikátní ID článku.
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Titulek článku.
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Titulek článku nemůže být prázdný!")
     */
    private $title;

    /**
     * @var string Text (obsah) článku.
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Obsah článku nemůže být prázdný!")
     */
    private $content;

    /**
     * @var string Unikátní URL adresa článku.
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="URL adresa článku nemůže být prázdná!")
     */
    private $url;

    /**
     * @var string Krátký popis článku.
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Popis článku nemůže být prázdný!")
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}