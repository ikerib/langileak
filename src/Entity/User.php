<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $deparment;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $displayname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $dn;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $enabled;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $hizkuntza;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lanpostua;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ldapsaila;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nan;

    #[ORM\Column(type: 'text', nullable: true)]
    private $notes;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $sailburuada;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $surname;

    #[ORM\Column(type: 'json', nullable: true)]
    private $ldapTaldeak = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private $ldapRolak = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $password;

    /******************************************************************************************************************/
    /******************************************************************************************************************/
    /******************************************************************************************************************/

    #[Pure] public function __construct()
    {
        $this->notification = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->username;
    }


    /******************************************************************************************************************/
    /******************************************************************************************************************/
    /******************************************************************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDeparment(): ?string
    {
        return $this->deparment;
    }

    public function setDeparment(?string $deparment): self
    {
        $this->deparment = $deparment;

        return $this;
    }

    public function getDisplayname(): ?string
    {
        return $this->displayname;
    }

    public function setDisplayname(?string $displayname): self
    {
        $this->displayname = $displayname;

        return $this;
    }

    public function getDn(): ?string
    {
        return $this->dn;
    }

    public function setDn(?string $dn): self
    {
        $this->dn = $dn;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getHizkuntza(): ?string
    {
        return $this->hizkuntza;
    }

    public function setHizkuntza(?string $hizkuntza): self
    {
        $this->hizkuntza = $hizkuntza;

        return $this;
    }

    public function getLanpostua(): ?string
    {
        return $this->lanpostua;
    }

    public function setLanpostua(?string $lanpostua): self
    {
        $this->lanpostua = $lanpostua;

        return $this;
    }

    public function getLdapsaila(): ?string
    {
        return $this->ldapsaila;
    }

    public function setLdapsaila(?string $ldapsaila): self
    {
        $this->ldapsaila = $ldapsaila;

        return $this;
    }

    public function getNan(): ?string
    {
        return $this->nan;
    }

    public function setNan(?string $nan): self
    {
        $this->nan = $nan;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSailburuada(): ?bool
    {
        return $this->sailburuada;
    }

    public function setSailburuada(?bool $sailburuada): self
    {
        $this->sailburuada = $sailburuada;

        return $this;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getLdapTaldeak(): ?array
    {
        return $this->ldapTaldeak;
    }

    public function setLdapTaldeak(?array $ldapTaldeak): self
    {
        $this->ldapTaldeak = $ldapTaldeak;

        return $this;
    }

    public function getLdapRolak(): ?array
    {
        return $this->ldapRolak;
    }

    public function setLdapRolak(?array $ldapRolak): self
    {
        $this->ldapRolak = $ldapRolak;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
