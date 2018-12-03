<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }


    /**
     * @Route("/mdp/forgot", name="MDP.forgot")
     */
    public function mdpOublie(Environment $twig, Request $request){

        return new Response($twig->render('security/mdpForgot.html.twig'));
    }


    /*envoie mdp temporaire par mail*/
    /**
     * @Route("/mdp/forgot/validmail", name="MDP.validmail.forgot")
     */
    public function validMailMDPForgot(RegistryInterface $doctrine, Environment $twig, UserPasswordEncoderInterface $passwordEncoder, ObjectManager $manager, \Swift_Mailer $mailer){
        $donnees['username'] = htmlspecialchars($_POST['username']);
        $donnees['email'] = htmlspecialchars($_POST['email']);

        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$donnees['username']]);

        if ($donnees['email'] != $user->getEmail()){
            $erreurs = "aucun utilisateur avec ce nom et cet email !";
            return new Response($twig->render('security/mdpForgot.html.twig',['erreurs'=>$erreurs]));
        }else{
            $newPassword = "";

            $chaine = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ023456789+@!$%?&";
            $longeur_chaine = strlen($chaine);

            for($i = 1; $i <= 12; $i++)
            {
                $place_aleatoire = mt_rand(0,($longeur_chaine-1));
                $newPassword .= $chaine[$place_aleatoire];
            }

            $encodePassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodePassword);

            $manager->persist($user);
            $manager->flush();

            $message = (new \Swift_Message('Reset Password'))
                ->setFrom('projetpoker@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/newPassword.html.twig',
                        array('name' => $user->getUsername(), 'mdp'=>$newPassword)
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            return $this->redirectToRoute('index.index');
        }

    }
}