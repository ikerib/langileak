<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'contract:list']],
        'post'=> ['denormalization_context' => ['groups' => 'contract:write']],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => 'contract:item']],
        'put' => ['normalization_context' => ['groups' => 'contract:item']],
        'delete' => ['normalization_context' => ['groups' => 'contract:item']],
        ],
    order: ['startDate' => 'ASC', 'endDate' => 'ASC'],
    paginationEnabled: false,
)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['contract:list', 'contract:item','employee:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['contract:list', 'contract:item','employee:item','contract:write'])]
    private $name;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['contract:list', 'contract:item','employee:item','contract:write'])]
    private $startDate;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['contract:list', 'contract:item','employee:item','contract:write'])]
    private $endDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['contract:list', 'contract:item','employee:item'])]
    private $days;

    /************************************************************************************************/
    /************************************************************************************************/
    /************************************************************************************************/

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'contracts')]
    private $employee;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'contracts')]
    #[Groups(['contract:list', 'contract:item','employee:item'])]
    private $taldea;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isValid;

    public function __toString(): string
    {
        return $this->name;
    }

    /************************************************************************************************/
    /************************************************************************************************/
    /************************************************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDaysTotal(): ?int
    {
        return $this->daysTotal;
    }

    public function setDaysTotal(?int $daysTotal): self
    {
        $this->daysTotal = $daysTotal;

        return $this;
    }

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(?int $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getTaldea(): ?Group
    {
        return $this->taldea;
    }

    public function setTaldea(?Group $taldea): self
    {
        $this->taldea = $taldea;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }
}
