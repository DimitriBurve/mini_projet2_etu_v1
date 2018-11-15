<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 10/11/2018
 * Time: 14:18
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LigneCommandeRepository")
 * @ORM\Table(name="ligneCommandes")
 */
class LigneCommande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Commande")
     * @ORM\JoinColumn(name="Commande_id", referencedColumnName="id")
     */
    private $commandeId;

    /**
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumn(name="Produit_id", referencedColumnName="id")
     */
    private $produitId;

    /**
     * @ORM\Column(name="prix", type="decimal",  precision=6, scale=2, nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCommandeId()
    {
        return $this->commandeId;
    }

    public function setCommandeId($commandeId): self
    {
        $this->commandeId = $commandeId;
        return $this;
    }

    public function getProduitId()
    {
        return $this->produitId;
    }

    public function setProduitId($produitId): self
    {
        $this->produitId = $produitId;
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



}