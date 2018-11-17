<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 10/11/2018
 * Time: 13:26
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
* @ORM\Table(name="panier")
*/
class Panier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="User_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumn(name="Produit_id", referencedColumnName="id")
     */
    private $produitId;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAchat;

    /**
     * @ORM\Column(name="prix", type="decimal",  precision=6, scale=2, nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\Column(name="valid", type="boolean",nullable=true)
     */
    private $valid;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getProduitId()
    {
        return $this->produitId;
    }

    public function setProduitId($produitId): self
    {
        $this->produitId = $produitId;
        return $this;
    }

    public function getDateAchat()
    {
        return $this->dateAchat;
    }

    public function setDateAchat($dateAchat): self
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function setQuantite($quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValid()
    {
        return $this->valid;
    }

    public function setValid($valid): self
    {
        $this->valid = $valid;
        return $this;
    }



}