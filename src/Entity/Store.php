<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Store
 *
 * @ORM\Table(name="store", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_8D93D649E7927C74", columns={"name"})})
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 */
class Store
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    private $value = null;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param int $id
     * @return Store
     */
    public function setId(int $id): Store
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return Store
     */
    public function setName(string $name): Store
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param $value
     * @return Store
     */
    public function setValue($value = null): Store
    {
        $this->value = serialize($value);
        $this->parseType($value);
        return $this;
    }

    /**
     */
    public function getValue()
    {
        return $this->value ? unserialize($this->value) : $this->value;
    }

    /**
     * @param $value
     * @return Store
     */
    private function parseType($value): Store
    {
        $type = gettype($value);
        if ($type == 'object') {
            $class = @get_class($value);
            if ($class) {
                $type = $class;
            }
        }
        $this->type = $type ? : 'unknown';
        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
