<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 *
 * @ORM\Table(name="parameter", indexes={@ORM\Index(name="idx-parameter-domain_id", columns={"domain_id"}), @ORM\Index(name="idx-parameter-param_id", columns={"parameter_id"}), @ORM\Index(name="idx-parameter-type", columns={"type"}), @ORM\Index(name="idx-parameter-updated_at", columns={"updated_at"})})
 * @ORM\Entity
 */
class Parameter
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
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false, options={"comment"="actual history"})
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=150, nullable=true)
     */
    private $value;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \Domain
     *
     * @ORM\ManyToOne(targetEntity="Domain")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domain_id", referencedColumnName="id")
     * })
     */
    private $domain;

    /**
     * @var \Parameters
     *
     * @ORM\ManyToOne(targetEntity="Parameters")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     * })
     */
    private $parameter;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return Parameters
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param Parameters $parameter
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }
    
}
