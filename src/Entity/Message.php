<?php

namespace App\Entity;

use Faker\Factory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{

    /**
     * @var Factory boolean
     *
     * if user factory should be build generate random date from construct
     */
    const FACTORY = true ;

    public function __construct( $factory = false ) {

        if( !$factory ) {
            // real account
            $this->sendAt = new \DateTime() ;
        } else {

            // generate random date create account for this factory user
            $faker = Factory::create('fr_FR') ;

            $maxDate = 'now' ;
            $timezone = 'Europe/Paris' ;

            $this->sendAt = $faker->dateTime( $maxDate , $timezone ) ;
        }
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
     * @NotBlank( message="content message cant be empty" )
     * @Length(
     *      min="2" ,
     *      max="1024" ,
     *      minMessage="message size min is 2 characters" ,
     *      maxMessage="message size max is 1024 characters" ,
     *      normalizer="trim" )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Length(
     *      max="255" ,
     *      maxMessage="title message size max is 255 characters" ,
     *      normalizer="trim" )
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MessageBox", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $box;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemove = false;

    /**
     * @ORM\Column(type="datetime" , nullable=true )
     */
    private $removeAt = null;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getIsRemove(): ?bool
    {
        return $this->isRemove;
    }

    public function setIsRemove(bool $isRemove): self
    {
        $this->isRemove = $isRemove;

        if( !!$this->isRemove ) {

            $this->removeAt = new \DateTime() ;
        }

        return $this;
    }

    public function getRemoveAt(): ?\DateTimeInterface
    {
        return $this->removeAt;
    }

    public function setRemoveAt(\DateTimeInterface $removeAt): self
    {
        $this->removeAt = $removeAt;

        return $this;
    }
}
