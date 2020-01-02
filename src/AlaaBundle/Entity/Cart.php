<?php

namespace AlaaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="AlaaBundle\Repository\CartRepository")
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="IdProduct", type="integer")
     */
    private $idProduct;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAchat", type="date")
     */
    private $dateAchat;

    /**
     * @var bool
     *
     * @ORM\Column(name="delivrer", type="boolean")
     */
    private $delivrer;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Cart
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return Cart
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return int
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set dateAchat
     *
     * @param \DateTime $dateAchat
     *
     * @return Cart
     */
    public function setDateAchat($dateAchat)
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    /**
     * Get dateAchat
     *
     * @return \DateTime
     */
    public function getDateAchat()
    {
        return $this->dateAchat;
    }

    /**
     * Set delivrer
     *
     * @param boolean $delivrer
     *
     * @return Cart
     */
    public function setDelivrer($delivrer)
    {
        $this->delivrer = $delivrer;

        return $this;
    }

    /**
     * Get delivrer
     *
     * @return bool
     */
    public function getDelivrer()
    {
        return $this->delivrer;
    }
}

