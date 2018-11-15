<?php

namespace App\Controller;


use App\Entity\Panier;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;


class IndexController extends Controller
{
    /**
     * @Route("/", name="index.index")
     */
    public function index(Request $request, Environment $twig, RegistryInterface $doctrine)
    {

//        if(! is_null($this->getUser())){
//            echo "<br>";
//            echo " id: ".$this->getUser()->getId();
//            echo " roles :   ";
//            print_r($this->getUser()->getRoles());
//            die();
//        }

        $produits=$doctrine->getRepository(Produit::class)->findAll();
        $panier=$doctrine->getRepository(Panier::class)->findAll();
        $prixTotal=0;
//        if ($this->getUser()->getId() == $panier->getUserId())
        for ($i=0;$i<count($panier);$i++){
            $prixTotal = $prixTotal + $panier[$i]->getPrix();
        }

        if($this->isGranted('ROLE_ADMIN')) {
            //return $this->redirectToRoute('admin.index');
            return new Response($twig->render('backOff/backOFFICE.html.twig'));
        }
        if($this->isGranted('ROLE_CLIENT')) {
           // return $this->redirectToRoute('panier.index');
            return new Response($twig->render('frontOff/frontOFFICE.html.twig',['id'=>$this->getUser()->getId(),'produits'=>$produits, 'panier'=>$panier,'prixTotal'=>$prixTotal]));
        }
        return new Response($twig->render('accueil.html.twig'));

    }

    /**
     * @Route("/client", name="index.client")
     */
    public function indexClient(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $produits=$doctrine->getRepository(Produit::class)->findAll();
        if($this->isGranted('ROLE_ADMIN')) {
            //return $this->redirectToRoute('admin.index');
            return new Response($twig->render('backOff/backOFFICE.html.twig'));
        }
        if($this->isGranted('ROLE_CLIENT')) {
            return $this->redirectToRoute('Client.index');
            //return new Response($twig->render('frontOff/frontOFFICE.html.twig'));
        }
        return new Response($twig->render('accueil.html.twig'));

    }
}
