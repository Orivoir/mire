<?php

namespace App\Entity;


use Faker\Factory;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{

    /**
     * @var Factory boolean
     *
     * if user factory should be build generate random date from construct
     */
    const FACTORY = true ;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("public")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("public")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublic = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemove = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removeAt = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isWarningPublic = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentary", mappedBy="article", orphanRemoval=true)
     */
    private $commentaries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundName = NULL;

    public function getSlug(): ?string {

        return ( new Slugify() )->slugify( $this->title ) ;
    }

    public function __construct( $factory = false )
    {
        $this->commentaries = new ArrayCollection() ;

        if( !$factory ) {
            // real account
            $this->createAt = new \DateTime() ;
        } else {
            // generate random date create account for this factory user
            $faker = Factory::create('fr_FR') ;

            $maxDate = 'now' ;
            $timezone = 'Europe/Paris' ;

            $this->createAt = $faker->dateTime( $maxDate , $timezone ) ;
        }
    }

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

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

    public function getIsWarningPublic(): ?bool
    {
        return $this->isWarningPublic;
    }

    public function setIsWarningPublic(bool $isWarningPublic): self
    {
        $this->isWarningPublic = $isWarningPublic;

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

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentariesVisible(): Collection {

        $allCommentaries = $this->commentaries ;

        $visibleCommentaries = new ArrayCollection() ;

        foreach( $allCommentaries as $commentary ) {

            if( !$commentary->getIsRemove() ) {

                $visibleCommentaries[] = $commentary ;
            }

        }

        return $visibleCommentaries ;
    }

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentaries(): Collection
    {
        return $this->commentaries;
    }

    public function addCommentary(Commentary $commentary): self
    {
        if (!$this->commentaries->contains($commentary)) {
            $this->commentaries[] = $commentary;
            $commentary->setArticle($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ( $this->commentaries->contains($commentary) ) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getArticle() === $this) {
                $commentary->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return integer - days from create at
     */
    public function getDaysCreateAt(): ?int {

        $timestampUnixFromCreateArticle = ( ( new \DateTime() )->getTimestamp() ) - ( $this->createAt->getTimestamp() ) ;

        return $timestampUnixFromCreateArticle / 60 / 60 / 24 ;
    }

    public function getBackgroundName(): ?string
    {
        return $this->backgroundName;
    }

    public function setBackgroundName(?string $backgroundName): self
    {
        $this->backgroundName = $backgroundName;

        return $this;
    }
}
