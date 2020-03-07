<?php

namespace App\Entity;

use Faker\Factory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sendAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="contacts")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @NotBlank( message="content contact cant be empty" )
     * @Length(
     *      min="2" ,
     *      max="1024" ,
     *      minMessage="contact content size min is 2 characters" ,
     *      maxMessage="commentary size max is 1024 characters" ,
     *      normalizer="trim" )
     */
    private $content;

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

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
