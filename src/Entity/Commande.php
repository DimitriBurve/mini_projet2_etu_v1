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
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 * @ORM\Table(name="commandes")
 */
class Commande
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
     * @ORM\ManyToOne(targetEntity="Etat")
     * @ORM\JoinColumn(name="Etat_id", referencedColumnName="id")
     */
    private $etatId;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(name="prix", type="decimal",  precision=6, scale=2, nullable=true)
     */
    private $prixTotal;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getEtatId()
    {
        return $this->etatId;
    }

    public function setEtatId($etatId): self
    {
        $this->etatId = $etatId;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }

    public function setPrixTotal($prixTotal): self
    {
        $this->prixTotal = $prixTotal;
        return $this;
    }


}