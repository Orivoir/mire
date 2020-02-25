<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{

    public function __construct() {

        $this->sendAt = new \DateTime();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MessageBox", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $box;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBox(): ?MessageBox
    {
        return $this->box;
    }

    public function setBox(?MessageBox $box): self
    {
        $this->box = $box;

        return $this;
    }
}
