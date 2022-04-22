<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete'],
    denormalizationContext: ['groups' => ['employee:list','employee:item']],
    normalizationContext: ['groups' => ['employee:list','employee:item']],
    order: ['surname' => 'ASC', 'name' => 'ASC'],
    paginationEnabled: false
)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["employee:list", "employee:item"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["employee:list", "employee:item"])]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private ?string $surname;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $code;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $dateStartWorking;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $years;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $months;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $days;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $dateTeoric;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $dateTriennium;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $datePayroll;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $numberTrienniums;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["employee:list", "employee:item"])]
    private $numberDaysOf;

    /*************************************************************************************************/
    /*************************************************************************************************/
    /*************************************************************************************************/

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Contract::class, cascade: ["persist"])]
    #[Groups(["employee:item"])]
    private $contracts;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    /*************************************************************************************************/
    /*************************************************************************************************/
    /*************************************************************************************************/


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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts[] = $contract;
            $contract->setEmployee($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getEmployee() === $this) {
                $contract->setEmployee(null);
            }
        }

        return $this;
    }

    public function getDateStartWorking(): ?\DateTimeInterface
    {
        return $this->dateStartWorking;
    }

    public function setDateStartWorking(?\DateTimeInterface $dateStartWorking): self
    {
        $this->dateStartWorking = $dateStartWorking;

        return $this;
    }

    public function getYears(): ?int
    {
        return $this->years;
    }

    public function setYears(?int $years): self
    {
        $this->years = $years;

        return $this;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function setMonths(?int $months): self
    {
        $this->months = $months;

        return $this;
    }

    public function getDateTeoric(): ?\DateTimeInterface
    {
        return $this->dateTeoric;
    }

    public function setDateTeoric(?\DateTimeInterface $dateTeoric): self
    {
        $this->dateTeoric = $dateTeoric;

        return $this;
    }

    public function getDateTriennium(): ?\DateTimeInterface
    {
        return $this->dateTriennium;
    }

    public function setDateTriennium(?\DateTimeInterface $dateTriennium): self
    {
        $this->dateTriennium = $dateTriennium;

        return $this;
    }

    public function getDatePayroll(): ?\DateTimeInterface
    {
        return $this->datePayroll;
    }

    public function setDatePayroll(?\DateTimeInterface $datePayroll): self
    {
        $this->datePayroll = $datePayroll;

        return $this;
    }

    public function getNumberTrienniums(): ?int
    {
        return $this->numberTrienniums;
    }

    public function setNumberTrienniums(?int $numberTrienniums): self
    {
        $this->numberTrienniums = $numberTrienniums;

        return $this;
    }

    public function getNumberDaysOf(): ?int
    {
        return $this->numberDaysOf;
    }

    public function setNumberDaysOf(?int $numberDaysOf): self
    {
        $this->numberDaysOf = $numberDaysOf;

        return $this;
    }


}
