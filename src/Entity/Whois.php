<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Whois
 *
 * @ORM\Table(name="whois", indexes={@ORM\Index(name="idx-whois-type", columns={"type"}), @ORM\Index(name="idx-whois-updated_at", columns={"updated_at"}), @ORM\Index(name="idx-whois-domain_id", columns={"domain_id"})})
 * @ORM\Entity
 */
class Whois
{
    const TYPE_ACTUAL = 1;
    const TYPE_HISTORY = 2;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false, options={"comment"="actualhistory"})
     */
    private $type;

    /**
     * @var Domain
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Domain")
     * @ORM\JoinColumn(name="domain_id", referencedColumnName="id")
     */
    private $domain;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null|string $data
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }


    /**
     * @return Domain
     */
    public function getDomainName()
    {
        return $this->domain->getName();
    }
}
