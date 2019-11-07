<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     */
    private $email;

//    /**
//     * @ORM\Column(type="json")
//     */
//    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBloger;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uniqueHash;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="subscribedToMe")
     * * @ORM\JoinTable(name="user_user",
     *      joinColumns={@ORM\JoinColumn(name="user_source", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_target", referencedColumnName="id")}
     *      )
     */
    private $mySubscribes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="mySubscribes")
     */
    private $subscribedToMe;

    public function __construct()
    {
        $this->mySubscribes = new ArrayCollection();
        $this->subscribedToMe = new ArrayCollection();
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
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [$this->getRoleId()->getRole()];
    }

    // TODO: solve setRole problem
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
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIsBloger(): ?bool
    {
        return $this->isBloger;
    }

    public function setIsBloger(bool $isBloger): self
    {
        $this->isBloger = $isBloger;

        return $this;
    }

    public function getRoleId(): ?Role
    {
        return $this->role_id;
    }

    public function setRoleId(?Role $role_id): self
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUniqueHash(): ?string
    {
        return $this->uniqueHash;
    }

    public function setUniqueHash(string $uniqueHash): self
    {
        $this->uniqueHash = $uniqueHash;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getMySubscribes(): Collection
    {
        return $this->mySubscribes;
    }

    public function addMySubscribe(self $mySubscribe): self
    {
        if (!$this->mySubscribes->contains($mySubscribe)) {
            $this->mySubscribes[] = $mySubscribe;
        }

        return $this;
    }

    public function removeMySubscribe(self $mySubscribe): self
    {
        if ($this->mySubscribes->contains($mySubscribe)) {
            $this->mySubscribes->removeElement($mySubscribe);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscribedToMe(): Collection
    {
        return $this->subscribedToMe;
    }

    public function addSubscribedToMe(self $subscribedToMe): self
    {
        if (!$this->subscribedToMe->contains($subscribedToMe)) {
            $this->subscribedToMe[] = $subscribedToMe;
            $subscribedToMe->addMySubscribe($this);
        }

        return $this;
    }

    public function removeSubscribedToMe(self $subscribedToMe): self
    {
        if ($this->subscribedToMe->contains($subscribedToMe)) {
            $this->subscribedToMe->removeElement($subscribedToMe);
            $subscribedToMe->removeMySubscribe($this);
        }

        return $this;
    }
}
