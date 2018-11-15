<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 08/11/2018
 * Time: 11:00
 */

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Etat;
use App\Entity\LigneCommande;
use App\Entity\Panier;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use App\Entity\Produit;
use App\Form\ProduitType;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Validator\Constraints\Date;
use Twig\Environment;                            // template TWIG
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
// dans les annotations @Method

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;  // annotation security

class ClientController extends Controller
{
    /**
     * @Route("/produitsClient", name="Client.index")
     */
    public function indexClient(){
        return $this->redirectToRoute('ProduitsClient.show');
    }

    /**
     * @Route("/produitsClient/show", name="ProduitsClient.show")
     */
    public function showProduitsClient(Request $request, Environment $twig, RegistryInterface $doctrine){
        $produits=$doctrine->getRepository(Produit::class)->findAll();
        $nbProduitsSelect=0;
        return new Response($twig->render('frontOff/Produit/showProduits.html.twig', ['produits' => $produits, 'nbProduitsSelect' => $nbProduitsSelect]));
    }

    /**
     * @Route("/verifAddPanier", name="Panier.verifAdd",methods={"POST"})
     */
    public function verifAddPanier(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){

        $entityManager = $this->getDoctrine()->getManager();

        $id=htmlspecialchars($_POST['produitId']);

        $produit= $doctrine->getRepository(Produit::class)->find($id);
        $panier=$entityManager->getRepository(Panier::class)->findOneBy(['produitId' => $id, 'userId' =>$this->getUser()->getId()]);

        $prix=$produit->getPrix();

        if (!$panier){

            $panier = new Panier();
            $panier->setPrix($prix);
            $panier->setProduitId($produit);
            $panier->setQuantite(1);
            $datePanier = new \DateTime();
            $panier->setDateAchat($datePanier);
            $panier->setUserId($this->getUser());

            $manager->persist($panier);
            $manager->flush();


            $produit->setStock($produit->getStock()-1);

            $manager->persist($produit);
            $manager->flush();

            return $this->redirectToRoute('index.index');
        }else{
            $panier->setQuantite($panier->getQuantite()+1);

            $entityManager->flush();


            $produit->setStock($produit->getStock()-1);

            $manager->persist($produit);
            $manager->flush();
        }

        return $this->redirectToRoute('index.index');
    }

    /**
     * @Route("/produitClientDeletePanier", name="ProduitClientPanier.delete")
     */
    public function deleteProduitClient(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){
        $entityManager = $this->getDoctrine()->getManager();

        $id=htmlspecialchars($_POST['produitId']);

        $panier=$entityManager->getRepository(Panier::class)->find($id);
        $produit= $doctrine->getRepository(Produit::class)->find($panier->getProduitId());


        $quantite = $panier->getQuantite();
        if ($panier->getQuantite()-1 != 0) {
            $panier->setQuantite($panier->getQuantite() - 1);
        }else{
            $entityManager->remove($panier);
        }

        $produit->setStock($produit->getStock()+1);

        $entityManager->flush();

        return $this->redirectToRoute('index.index');
    }

    /**
     * @Route("/show/PanierClient", name="panier.show")
     */
    public function showPanierClient(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){
        $panier=$doctrine->getRepository(Panier::class)->findAll();

        return new Response($twig->render('frontOff/panier/panierFrontOffice.html.twig',['panier'=>$panier]));
    }

    /**
     * @Route("/valid/panier", name="Panier.valid")
     */
    public function validPanier(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){
        $entityManager = $this->getDoctrine()->getManager();

        $commande = new Commande();
        $commande->setUserId($this->getUser());
        $etat= new Etat();
        $commande->setEtatId($etat);
        $commande->setDate(new \DateTime());
        $panier= $doctrine->getRepository(Panier::class)->findAll();
        $prixTotal=0;
        for ($i=0;$i<count($panier);$i++){
            $prixTotal = $prixTotal + $panier[$i]->getPrix();
        }
        $commande->setPrixTotal($prixTotal);

        return new Response($twig->render('frontOff/panier/panierFrontOffice.html.twig',['panier'=>$panier, 'prixTotal'=>$commande->getPrixTotal()]));
    }

}