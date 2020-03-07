<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields={"username"},
 *  message="There is already an account with this username"
 * )
 * @Table(name="client")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @NotBlank( message="username cant be empty" )
     * @Length(
     *      min=2, max=42 ,
     *      minMessage="username min size is 2 characters" ,
     *      maxMessage="username max size is 42 characters",
     *      normalizer="trim"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Email(
     *      message="email format is invalid"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @NotEqualTo( propertyPath="name" , message="first name cant be equal to last name" )
     * @Length(
     *      min=2, max=30 ,
     *      minMessage="first name min size is 2 characters" ,
     *      maxMessage="first name max size is 42 characters",
     * )
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @NotEqualTo( propertyPath="fname" , message="last name cant be equal to first name" )
     * @Length(
     *      min=2, max=30 ,
     *      minMessage="last name min size is 2 characters" ,
     *      maxMessage="last name max size is 42 characters",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemove = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removeAt = null;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublicEmail = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublicProfil = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user", orphanRemoval=true)
     */
    private $articles;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MessageBox", mappedBy="user", cascade={"persist", "remove"})
     */
    private $messageBox;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentary", mappedBy="user", orphanRemoval=true)
     */
    private $commentaries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $blockers = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contact", mappedBy="user")
     */
    private $contacts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarName=null;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $tokenActivate;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->commentaries = new ArrayCollection();
        $this->createAt = new \DateTime() ;

        // token for CSRF Fail this token is shared with JavaScript
        $this->token = \md5( \str_shuffle( "le chat aime les arbres :-)" ) ) ;

        // token for activate account not shared with JavaScript
        $this->tokenActivate = \md5( \str_shuffle( "le chat n'aime pas les arbres :-)" ) ) ;

        $this->messages = new ArrayCollection();

        $this->messageBox = new MessageBox() ;
        $this->messageBox->setUser( $this ) ;
        $this->contacts = new ArrayCollection();
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword ;

        return $this ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFname(): ?string
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

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

    public function getIsRemove(): ?bool
    {
        return $this->isRemove;
    }

    public function setIsRemove(bool $isRemove): self
    {
        $this->isRemove = $isRemove;

        if(!!$this->isRemove ) {

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIsPublicEmail(): ?bool
    {
        return $this->isPublicEmail;
    }

    public function setIsPublicEmail(bool $isPublicEmail): self
    {
        $this->isPublicEmail = $isPublicEmail;

        return $this;
    }

    public function getIsPublicProfil(): ?bool
    {
        return $this->isPublicProfil;
    }

    public function setIsPublicProfil(bool $isPublicProfil): self
    {
        $this->isPublicProfil = $isPublicProfil;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    public function getMessageBox(): ?MessageBox
    {
        return $this->messageBox;
    }

    public function setMessageBox(MessageBox $messageBox): self
    {
        $this->messageBox = $messageBox;

        // set the owning side of the relation if necessary
        if ($messageBox->getUser() !== $this) {
            $messageBox->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getSubjects() {

        $commentaries = $this->getCommentaries() ;

        $articles = new ArrayCollection() ;

        foreach( $commentaries as $commentary ) {

            $article = $commentary->getArticle() ;

            if(
                !$articles->contains( $article ) &&
                !$article->getIsRemove()
            ) {

                $articles[] = $article ;
            }

        }

        return $articles ;

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
            $commentary->setUser($this);
        }

        return $this;
    }

    public function removeCommentary(Commentary $commentary): self
    {
        if ($this->commentaries->contains($commentary)) {
            $this->commentaries->removeElement($commentary);
            // set the owning side to null (unless already changed)
            if ($commentary->getUser() === $this) {
                $commentary->setUser(null);
            }
        }

        return $this;
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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * check if an user is already blocked
     */
    public function isBlocked( User $user ): bool {

        $id = $user->getId() ;
        $blockers = $this->getBlockers() ;

        $isBlock = false ;

        foreach( $blockers as $blocker ) {

            if( $blocker == $id ) {

                $isBlock = true ;
            }
        }

        return $isBlock ;
    }

    /**
     * contains list id user blocked by this user
     */
    public function getBlockers(): ?array
    {
        return $this->blockers;
    }

    public function setBlockers(?array $blockers): self
    {
        $this->blockers = $blockers;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(string $avatarName): self
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    public function getAvatarPath() {

        return "/assets" . (!!$this->avatarName ? '/uploads/avatar/' . $this->avatarName : '/images/pawn.svg' ) ;
    }

    public function getTokenActivate(): ?string
    {
        return $this->tokenActivate;
    }

    public function setTokenActivate(string $tokenActivate): self
    {
        $this->tokenActivate = $tokenActivate;

        return $this;
    }

}
