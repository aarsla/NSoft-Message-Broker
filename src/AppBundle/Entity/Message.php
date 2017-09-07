<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
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
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=false)
     * @Groups({"producer"})
     */
    private $amount;

    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     * @Groups({"producer"})
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
        return $this->amount ? $this->amount : 'Message without amount';
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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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