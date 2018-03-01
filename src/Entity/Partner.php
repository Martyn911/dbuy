<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Partner
 *
 * @ORM\Table(name="partner", uniqueConstraints={@ORM\UniqueConstraint(name="name_UNIQUE", columns={"name"})})
 * @ORM\Entity
 */
class Partner
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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ref_url", type="string", length=255, nullable=true)
     */
    private $refUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=500, nullable=true)
     */
    private $comment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

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
    public function getRefUrl()
    {
        return $this->refUrl;
    }

    /**
     * @param null|string $refUrl
     */
    public function setRefUrl($refUrl)
    {
        $this->refUrl = $refUrl;
    }

    /**
     * @return null|string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param null|string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
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

}
