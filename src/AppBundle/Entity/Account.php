<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account
{
    use TimestampableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     * @Assert\NotBlank()
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=false)
     */
    private $balance;

    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currency = "EUR";
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->balance ? $this->balance : 'Account without balance';
    }

    /**
     * Getters and Setters
     */

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}