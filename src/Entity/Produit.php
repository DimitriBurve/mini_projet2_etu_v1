<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

// http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html#column
// http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 * @ORM\Table(name="produits")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nom", type="string", length=255)
     *
     * @Assert\NotBlank(
     *      message= "produit.nom.not_blank"
     * )
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "produit.nom.length.min",
     *      maxMessage = "produit.nom.length.max"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(name="prix", type="decimal",  precision=8, scale=2, nullable=true)
     * @Assert\NotBlank(
     *      message= "produit.prix.not_blank"
     * )
     * @Assert\Regex(
     *     pattern = "/^[0-9]{1,}\,{0,1}[0-9]{0,}$/",
     *     message = "Seulement un entier positif."
     *     )
     */
    private $prix;

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var boolean
     * @ORM\Column(name="disponible", type="boolean", nullable=true)
     */
    private $disponible;

    /**
     * @var int
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;



    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto( $photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDisponible()
    {
        return $this->disponible;
    }

    public function setDisponible( $disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock): self
    {
        $this->stock = $stock;

        return $this;
    }


    /**
     * @ORM\ManyToOne(targetEntity="TypeProduit")
     * @ORM\JoinColumn(name="typeProduit_id", referencedColumnName="id")
     * @Assert\NotBlank(
     *      message= "selectionner un type"
     * )
     */
    private $typeProduitId;

    /**
     * @return int
     */
    public function getTypeProduitId()
    {
        return $this->typeProduitId;
    }

    /**
     * @param int $typeProduitId
     * @return Produit
     */
    public function setTypeProduitId($typeProduitId)
    {
        $this->typeProduitId = $typeProduitId;
        return $this;
    }

}
