<?php

namespace App\Entity;

use Faker\Factory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaryRepository")
 */
class Commentary
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
     * @ORM\Column(type="text")
     * @NotBlank( message="content commentary cant be empty" )
     * @Length(
     *      min="2" ,
     *      max="512" ,
     *      minMessage="commentary size min is 2 characters" ,
     *      maxMessage="commentary size max is 512 characters" ,
     *      normalizer="trim" )
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemove = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removeAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

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

    public function setRemoveAt(?\DateTimeInterface $removeAt): self
    {
        $this->removeAt = $removeAt;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

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
}
