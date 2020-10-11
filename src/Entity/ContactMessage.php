<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactMessageRepository::class)
 */
class ContactMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Emailová adresa odesílatele.
     * @Assert\NotBlank(message = "Emailová adresa nemůže být prázdná!")
     * @Assert\Email(message="'{{ value }}' není validní emailová adresa!")
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @var string Obsah zprávy.
     * @Assert\Length(min=10, minMessage="Zpráva musí být minimálně {{ limit }} znaků dlouhá!")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter pro email odesílatele.
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter pro email odesílatele.
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Getter pro obsah zprávy.
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Setter pro obsah zprávy.
     * @param string|null $message
     * @return $this
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
