<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\CommentaireProduit;
use App\Entity\Etat;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Entity\User;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\LigneCommande;

class AppFixtures extends Fixture
{
    // ...https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadTypesProduits($manager);
        $this->loadProduits($manager);
        $this->loadUsers($manager);
        $this->loadEtatsCommandes($manager);
        ////////////
        /// TD
        ///
//        $this->testAjoutPanier($manager);
//        $this->affichePanier($manager);
//        $this->modifQuantite($manager);
//        $this->affichePanier($manager);
//        $this->transformePanierCommande($manager);
//        $this->affichePanier($manager);
//        $this->testAjoutPanier($manager);
    }
    // TD




    // fin TD
    public function loadTypesProduits(objectManager $manager){
        // les types de produits
        $typesProduits = [
            ['id' => 1,'libelle' => 'Fourniture de bureau'],
            ['id' => 2,'libelle' => 'Mobilier'],
            ['id' => 3,'libelle' => 'Divers']
        ];
        foreach ($typesProduits as $type)
        {
            echo $type['libelle']."\n";
            $type_new = new TypeProduit();
            $type_new->setLibelle($type['libelle']);
            $manager->persist($type_new);
            $manager->flush();
        }
    }

    public function loadProduits(objectManager $manager){
        echo " \n\nles produits : \n";
        $typeProduit[1]=$manager->getRepository(TypeProduit::class)->findOneBy(['libelle'=>'Fourniture de bureau']);
        $typeProduit[2]=$manager->getRepository(TypeProduit::class)->findOneBy(['libelle'=>'Mobilier']);
        // les produits

        $produits = [
            ['id' => 1,'nom' => 'Enveloppes (50p)', 'prix' => '2', 'typeProduit_id' => '1', 'photo' => 'Enveloppes (50p)'],
            ['id' => 2,'nom' => 'Stylo noir', 'prix' => '1', 'typeProduit_id' => '1', 'photo' => 'stylo.jpeg'],
            ['id' => 3,'nom' => 'Boite de rangement', 'prix' => '3', 'typeProduit_id' => '1', 'photo' => 'boites.jpeg'],
            ['id' => 4,'nom' => 'Chaise', 'prix' => '40', 'typeProduit_id' => '2', 'photo' => 'chaise.jpeg'],
            ['id' => 5,'nom' => 'Tables', 'prix' => '200', 'typeProduit_id' => '2', 'photo' => 'table.jpeg']
        ];

        foreach ($produits as $produit)
        {
            echo $produit['nom']." - ".$produit['prix']." € - ".$produit['typeProduit_id']."\n";
            $new_produit = new Produit();
            $new_produit->setNom($produit['nom']);
            $new_produit->setPrix($produit['prix']);
            $new_produit->setPhoto($produit['photo']);
            $new_produit->setStock(3);
            $new_produit->setTypeProduitId($typeProduit[$produit['typeProduit_id']]);
            $manager->persist($new_produit);
            $manager->flush();
        }
    }

    public function loadUsers(objectManager $manager){
        // les utilisateurs

        echo " \n\nles utilisateurs : \n";

        $admin = new User();
        $password = $this->encoder->encodePassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setRoles('ROLE_ADMIN')
            ->setUsername('admin')->setEmail('admin@example.com')->setIsActive('1');
        $manager->persist($admin);
        echo $admin."\n";

        $client = new User();
        $client->setRoles('ROLE_CLIENT')
            ->setUsername('client')->setEmail('client@example.com')->setIsActive('1');
        $password = $this->encoder->encodePassword($client, 'client');
        $client->setPassword($password);
        $manager->persist($client);
        echo $client."\n";

        $client2 = new User();
        $client2->setRoles('ROLE_CLIENT')
            ->setUsername('client2')->setEmail('client2@example.com')->setIsActive('1');
        $password = $this->encoder->encodePassword($client2, 'client2');
        $client2->setPassword($password);
        $manager->persist($client2);
        echo $client2."\n";
        $manager->flush();

    }
    public function loadEtatsCommandes(objectManager $manager)
    {
        // état de la commande
        $etat1 = new Etat();
        $etat1->setNom('A préparer');
        $manager->persist($etat1);
        $etat2 = new Etat();
        $etat2->setNom('Expédié');
        $manager->persist($etat2);
        $manager->flush();
    }

}