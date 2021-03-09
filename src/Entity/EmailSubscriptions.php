<?php

namespace App\Entity;

use App\ORM\Fields\ActiveFieldTrait;
use App\ORM\Fields\MetadataTrait;
use App\ORM\Fields\TimestampableTrait;
use App\ORM\Fields\UniqueIdTrait;
use App\Repository\EmailSubscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailSubscriptionsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class EmailSubscriptions
{
    use UniqueIdTrait;
    use ActiveFieldTrait;
    use TimestampableTrait;
    use MetadataTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $campaigns = [];

    /**
     * EmailSubscriptions constructor.
     */
    public function __construct()
    {
        $this->active = false;
        $this->setUniqueId();
        $this->updateTimestamps();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCampaigns(): ?array
    {
        return $this->campaigns;
    }

    public function setCampaigns(?array $campaigns): self
    {
        $this->campaigns = $campaigns;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestampsCall(): void
    {
        $this->updateTimestamps();
    }
}
