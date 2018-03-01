<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Zone
 *
 * @ORM\Table(name="zone", uniqueConstraints={@ORM\UniqueConstraint(name="name_UNIQUE", columns={"name"})}, indexes={@ORM\Index(name="idx-zone-status", columns={"status"}), @ORM\Index(name="idx-zone-idnname", columns={"idn_name"})})
 * @ORM\Entity
 */
class Zone
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="idn_name", type="string", length=100, nullable=true)
     */
    private $idn_name;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=150, nullable=true)
     */
    private $service;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getIdnName()
    {
        return $this->idn_name;
    }

    /**
     * @param string $idn_name
     */
    public function setIdnName($idn_name)
    {
        $this->idn_name = $idn_name;
    }
}
