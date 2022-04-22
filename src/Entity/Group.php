<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContractRepository;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'group:list']],
        'post'=> ['denormalization_context' => ['groups' => 'group:write']],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => 'group:item']],
        'put' => ['denormalization_context' => ['groups' => 'group:write']],
        'delete' => ['normalization_context' => ['groups' => 'group:item']]
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: false,
)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["group:list", "group:item",'employee:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["group:list", "group:item", "group:write",'employee:item'])]
    private $name;

    /*************************************************************************************************/
    /*************************************************************************************************/
    /*************************************************************************************************/

    #[ORM\OneToMany(mappedBy: 'taldea', targetEntity: Contract::class, cascade: ["persist"])]
    private $contracts;

    /*************************************************************************************************/
    /*************************************************************************************************/
    /*************************************************************************************************/

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

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
            $contract->setTaldea($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getTaldea() === $this) {
                $contract->setTaldea(null);
            }
        }

        return $this;
    }
}
