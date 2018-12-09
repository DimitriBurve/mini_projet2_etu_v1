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
use App\Entity\TypeProduit;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use App\Entity\Produit;
use App\Form\ProduitType;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;
use Twig\Environment;                            // template TWIG
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\HttpFoundation\Request;    // objet REQUEST
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
// dans les annotations @Method

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;  // annotation security

/**
 * @Security("has_role('ROLE_CLIENT')");
 */
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
        $panier=$entityManager->getRepository(Panier::class)->findOneBy(['produitId' => $id, 'userId' =>$this->getUser()->getId(),'valid'=>null]);
        $prix=$produit->getPrix();

        if (!$panier){
            $panier = new Panier();
            $panier->setPrix($prix);
            $panier->setProduitId($produit);
            $panier->setQuantite($_POST['number']);
            $datePanier = new \DateTime();
            $panier->setDateAchat($datePanier);
            $panier->setUserId($this->getUser());

            $manager->persist($panier);
            $manager->flush();


            $produit->setStock($produit->getStock()-$_POST['number']);

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

        if ($panier->getQuantite()-$_POST['number'] != 0) {
            $panier->setQuantite($panier->getQuantite() - $_POST['number']);
        }else{
            $entityManager->remove($panier);
        }

        $produit->setStock($produit->getStock()+$_POST['number']);

        $entityManager->flush();

        return $this->redirectToRoute('index.index');
    }


    /**
     * @Route("/show/PanierClient", name="panier.show")
     */
    public function showPanierClient(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){
        $panier=$doctrine->getRepository(Panier::class)->findBy(['userId'=>$this->getUser(),'valid'=>null]);

        $prixTotal=0;
        for ($i=0;$i<count($panier);$i++){
            $prixTotal = $prixTotal + $panier[$i]->getPrix()*$panier[$i]->getQuantite();
        }

        return new Response($twig->render('frontOff/panier/panierFrontOffice.html.twig',['id'=>$this->getUser()->getId(),'panier'=>$panier,'prixTotal'=>$prixTotal]));
    }


    /**
     * @Route("/valid/panier", name="Panier.valid")
     */
    public function validPanier(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){

        $commande = new Commande();
        $commande->setUserId($this->getUser());
        $etat=$manager->getRepository(Etat::class)->find(1);
        $commande->setEtatId($etat);
        $commande->setDate(new \DateTime());
        $panier = $doctrine->getRepository(Panier::class)->findBy(['userId'=>$this->getUser(),'valid'=>null]);
        $prixTotal=0;

        for ($i=0;$i<count($panier);$i++){
            $prixTotal = $prixTotal + $panier[$i]->getPrix()*$panier[$i]->getQuantite();
        }

        $commande->setPrixTotal($prixTotal);

        $manager->persist($commande);
        $manager->flush();

        $dateCommande = $doctrine->getRepository(Commande::class)->findBy(['userId'=>$this->getUser()],array('date'=>'ASC'));
        $dateTemp=null;
        $panierCommande=[];

        for ($i=0;$i<count($dateCommande);$i++){
            if ($dateCommande[$i]->getDate() < $commande->getDate()) {
                $dateTemp = $dateCommande[$i]->getDate();
            }
        }

        for ($i = 0; $i<count($panier); $i++) {
            if (($panier[$i]->getDateAchat() <= $commande->getDate()) and ($panier[$i]->getDateAchat() >= $dateTemp )) {
                array_push($panierCommande,$panier[$i]);
                $panier[$i]->setValid(true);
            }
        }

        for ($i=0;$i<count($panierCommande);$i++) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setPrix($panierCommande[$i]->getPrix());
            $ligneCommande->setQuantite($panierCommande[$i]->getQuantite());
            $ligneCommande->setCommandeId($commande);
            $ligneCommande->setProduitId($panierCommande[$i]->getProduitId());

            $manager->persist($ligneCommande);
            $manager->flush();
        }

        return $this->redirectToRoute('PanierValid.valid',['id'=>$commande->getId()]);
    }


    /**
     * @Route("/valid/panierValid{id}", name="PanierValid.valid")
     */
    public function panierValid(Environment $twig, RegistryInterface $doctrine,$id){
        $lignesCommande= $doctrine->getRepository(LigneCommande::class)->findBy(['commandeId'=>$id]);
        $commande = $doctrine->getRepository(Commande::class)->find($id);
        $prixTotal = $commande->getPrixTotal();


        return new Response($twig->render('frontOff/panier/validPanier.html.twig',['id'=>$this->getUser()->getId(),'panier'=>$lignesCommande,'prixTotal'=>$prixTotal]));
    }


    /**
     * @Route("/commandes/showAll/Front",name="commande.showAllCommandes")
     */
    public function showAllCommandes(RegistryInterface $doctrine, Environment $twig){
        $commandes = $doctrine->getRepository(Commande::class)->findAll();

        return new Response($twig->render('frontOff/commandes/allCommandesFrontOffice.html.twig',['commandes'=>$commandes,'id'=>$this->getUser()->getId()]));
    }


    /**
     * @Route("/commandes/detailsFront",name="CommandeClient.details")
     */
    public function showDetailsCommande(RegistryInterface $doctrine, Environment $twig){
        $id = htmlspecialchars($_POST['commandeId']);

        $lignesCommande= $doctrine->getRepository(LigneCommande::class)->findBy(['commandeId'=>$id]);

        $commande = $doctrine->getRepository(Commande::class)->find($id);
        $prixTotal = $commande->getPrixTotal();

        return new Response($twig->render('frontOff/commandes/detailsCommandeFrontOffice.html.twig',['lignesCommande'=>$lignesCommande,'prixTotal'=>$prixTotal]));
    }


    /**
     * @Route("/delete/Panier/Client", name="Panier.delete")
     */
    public function panierDelete(RegistryInterface $doctrine, ObjectManager $manager, Request $request){
        $panier = $doctrine->getRepository(Panier::class)->findBy(['userId'=>$this->getUser(),'valid'=>null]);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i=0;$i<count($panier);$i++){
            $entityManager->remove($panier[$i]);
            $produit= $doctrine->getRepository(Produit::class)->find($panier[$i]->getProduitId());
            $produit->setStock($produit->getStock()+1);
        }

        $entityManager->flush();

        return $this->redirectToRoute('panier.show');
    }


    /**
     * @Route("/coordonnees/show", name="Coordonnees.show")
     */
    public function coordonneesShow(RegistryInterface $doctrine, Request $request, Environment $twig){
        $coordonnees = $doctrine->getRepository(User::class)->find($this->getUser()->getId());

//        var_dump($coordonnees);

        return new Response($twig->render('frontOff/coordonnees/showCoordonnees.html.twig',['coordonnees'=>$coordonnees]));
    }


    /**
     * @Route("/update/coordonnees", name="Coordonnees.update")
     */
    public function editCoordonnees(RegistryInterface $doctrine, Request $request, Environment $twig){
        $coordonnees = $doctrine->getRepository(User::class)->find($this->getUser()->getId());

        return new Response($twig->render('frontOff/coordonnees/updateCoordonnees.html.twig',['coordonnees'=>$coordonnees]));
    }


    /**
     * @Route("update/valid/coordonnees", name="Coordonnees.validUpdate", methods={"PUT"})
     */
    public function validUpdateCoordonnees(Request $request, Environment $twig, RegistryInterface $doctrine, ObjectManager $manager){
        $donnees['nomClient'] = htmlspecialchars($_POST['nomClient']);
        $donnees['adresse'] = htmlspecialchars($_POST['adresse']);
        $donnees['codePostal'] = htmlspecialchars($_POST['codePostal']);
        $donnees['ville'] = htmlspecialchars($_POST['ville']);
        $donnees['email'] = htmlspecialchars($_POST['email']);

        $erreurs = array();
        if (!preg_match("/^[A-Za-z]{2,}/",$donnees['nomClient'])) $erreurs['nomClient'] = "nom composé de 2 lettres minimum";
        if (!preg_match("/^[A-Za-z]{2,}/",$donnees['ville'])) $erreurs['ville'] = "ville composée de 2 lettres minimum";
        if (!preg_match("#^[0-9]{5}$#",$donnees['codePostal'])) $erreurs['codePostal'] = "code postal composé de 5 chiffres";
        if (!preg_match("/^[A-Za-z0-9]{2,}/",$donnees['adresse'])) $erreurs['adresse'] = "adresse composée de 2 lettres minimum";
        if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) $erreurs['email']= "mail invalid";

        if (!$this->isCsrfTokenValid('form_valid',$request->get('token'))){
            return $this->render('error/errorToken.html.twig');
        }

        if (!empty($erreurs)){
            $coordonnees = $doctrine->getRepository(User::class)->find($this->getUser()->getId());

            return new Response($twig->render('frontOff/coordonnees/updateCoordonnees.html.twig',['coordonnees'=>$coordonnees,'erreurs'=>$erreurs, 'donnees'=>$donnees]));
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $coordonneesUpdate = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
            $coordonneesUpdate->setUsername($donnees['nomClient']);
            $coordonneesUpdate->setAdresse($donnees['adresse']);
            $coordonneesUpdate->setCodePostal($donnees['codePostal']);
            $coordonneesUpdate->setVille($donnees['ville']);
            $coordonneesUpdate->setEmail($donnees['email']);

            $manager->persist($coordonneesUpdate);
            $manager->flush();

            return $this->redirectToRoute('Coordonnees.show');
        }
    }


    /**
     * @Route("/update/coordonnees/mdp", name="Coordonnees.updateMDP")
     */
    public function updateMDP(Environment $twig, RegistryInterface $doctrine, AuthenticationUtils $authenticationUtils){

        return new Response($twig->render('frontOff/coordonnees/editMDP.html.twig'));
    }


    /**
     * @Route("/validUpdate/coordonnees/mdp", name="Coordonnees.validUpdateMDP", methods={"PUT"})
     */
    public function validUpdateMDP(RegistryInterface $doctrine, Environment $twig, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder){
        $donnees['mdp1'] = htmlspecialchars($_POST['mdp1']);
        $donnees['mdp2'] = htmlspecialchars($_POST['mdp2']);

        $erreurs = array();
        if (!preg_match("/^[A-Za-z]{2,}/",$donnees['mdp1'])) $erreurs['mdp1'] = "nom composé de 2 lettres minimum";
        if ($donnees['mdp2'] != $donnees['mdp1']) $erreurs['mdp2'] = "mots de passe différents rentrés";

        if (!empty($erreurs)){

            return new Response($twig->render('frontOff/coordonnees/editMDP.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]));
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $coordonneesUpdate = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

            $password = $passwordEncoder->encodePassword($this->getUser(), $donnees['mdp1']);
            $coordonneesUpdate->setPassword($password);

            $entityManager->persist($coordonneesUpdate);
            $entityManager->flush();

            return $this->redirectToRoute('Coordonnees.show');

        }
    }

    /**
     * @Route("/produits/filtres", name="Panier.filtres")
     */
    public function filter(Environment $twig, ObjectManager $doctrine){
        $produits = $doctrine->getRepository(Produit::class)->findAll();
        $typeProduits = $doctrine->getRepository(TypeProduit::class)->findAll();
        return new Response($twig->render('frontOff/filtres/showProduits.html.twig',['produits'=>$produits,'typeProduits'=>$typeProduits]));
    }
}