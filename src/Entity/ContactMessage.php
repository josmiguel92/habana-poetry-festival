<?php

namespace App\Entity;

use App\ORM\Fields\UniqueIdTrait;
use App\Repository\ContactMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use \App\ORM\Fields\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=ContactMessageRepository::class)
 */
class ContactMessage
{
    use TimestampableTrait;
    use UniqueIdTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $authorName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $subject;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $message;

    /**
     * ContactMessage constructor.
     */
    public function __construct()
    {
        $this->setUniqueId();
        $this->updateTimestamps();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
