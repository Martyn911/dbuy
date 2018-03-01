<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class WhoisCheck
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 63,
     *      minMessage = "whois.query_min_error",
     *      maxMessage = "whois.query_max_error"
     * )
     * @Assert\Regex("/^[a-zA-Zа-яА-Я0-9:.]+$/ui", message = "whois.wrong_query")
     */
    private $domain;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

}
