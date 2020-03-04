<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageBoxRepository")
 */
class MessageBox
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="messageBox", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="box", orphanRemoval=true)
     */
    private $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesVisible(): Collection {

        $messagesVisible = new ArrayCollection() ;
        $messages = $this->getMessages() ;

        foreach( $messages as $message ) {

            if( !$message->getIsRemove() ) {

                $messagesVisible[] = $message ;
            }
        }

        return $messagesVisible ;
    }

    /**
     * @return Collection|Message[]
     */
    public function getNewMessages(): Collection {

        $messagesNotRead = new ArrayCollection() ;
        $messages = $this->getMessages() ;

        foreach( $messages as $message ) {

            if( !$message->getIsRead() ) {

                $messagesNotRead[] = $message ;
            }
        }

        return $messagesNotRead ;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setBox($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getBox() === $this) {
                $message->setBox(null);
            }
        }

        return $this;
    }
}
