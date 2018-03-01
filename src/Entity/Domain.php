<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Domain
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 * @ORM\Table(name="domain", uniqueConstraints={@ORM\UniqueConstraint(name="idx-domain-name", columns={"name"})}, indexes={
 *     @ORM\Index(name="fk_domain_zone_idx", columns={"zone_id"}),
 *     @ORM\Index(name="idx-domain-zone_id", columns={"zone_id"}),
 *     @ORM\Index(name="idx-domain-status", columns={"status"}),
 *     @ORM\Index(name="idx-domain-created_at", columns={"created_at"}),
 *     @ORM\Index(name="idx-domain-modifed_at", columns={"modifed_at"}),
 *     @ORM\Index(name="idx-domain-expires_at", columns={"expires_at"}),
 *     @ORM\Index(name="idx-domain-updated_at", columns={"updated_at"}),
 *     @ORM\Index(name="idn-domain-idnname", columns={"idn_name"})
 * })
 * @ORM\Entity
 */
class Domain
{
    const DSTATUS_AVAILABLE = 'free';
    const DSTATUS_NOTAVAILABLE = 'busy';
    const DSTATUS_RESERVED = 'reserved';
    const DSTATUS_HOLD = 'hold';
    const DSTATUS_REDEMPTION = 'redemption';
    const DSTATUS_PENDING_DELETE = 'pendingDelete';

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="idn_name", type="string", length=255, nullable=true)
     */
    private $idn_name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dstatus", type="integer", nullable=true)
     */
    /**
     * @var \DomainStatus
     *
     * @ORM\ManyToOne(targetEntity="DomainStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dstatus_id", referencedColumnName="id")
     * })
     */
    private $dstatus;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modifed_at", type="datetime", nullable=true)
     */
    private $modifedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \Zone
     *
     * @ORM\ManyToOne(targetEntity="Zone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     * })
     */
    private $zone;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return !empty($this->idn_name) ? $this->idn_name : $this->name;
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
     * @return null|string
     */
    public function getDstatus()
    {
        return $this->dstatus;
    }

    /**
     * @param null|string $dstatus
     */
    public function setDstatus($dstatus)
    {
        $this->dstatus = $dstatus;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getModifedAt()
    {
        return $this->modifedAt;
    }

    /**
     * @param DateTime|null $modifedAt
     */
    public function setModifedAt($modifedAt)
    {
        $this->modifedAt = $modifedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param DateTime|null $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param Zone $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
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
