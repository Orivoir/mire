<?php

namespace App\Controller;

use App\Services\SendEmail;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // user already logged
        if ($this->getUser()) {

            return $this->redirectToRoute('app_user_profil' , [
                "username" => $this->getUser()->getUsername()
            ] , 302 );
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/activate/{token}" , methods={"GET"} , name="app_activate" )
     */
    public function activate( string $token , UserRepository $userRep ) {

        $user = $userRep->findOneBy( ['token' => $token] ) ;

        if( !$user ) {

            // 404 not exists user
            return $this->render('not-found.html.twig') ;

        } else if(
            $user->getId() !== $this->getUser()->getId() ||
            $user->getToken() !== $token
        ) {

            // 400 invalid token mute with 404
            return $this->render('not-found.html.twig') ;

        } else if( $user->getIsValid() ) {

            // 301 with already valid account

            $this->addFlash('success' , 'account activate with success' ) ;

            return $this->redirectToRoute('app_user_profil' , [
                "username" => $user->getUsername()
            ] , 301) ;

        } else {

            // 301 activate account with success

            $this->addFlash('success' , 'account activate with success' ) ;

            $user->setIsValid( true ) ;

            $em = $this->getDoctrine()->getManager() ;

            $em->persist( $user ) ;
            $em->flush() ;

            $mailer = new SendEmail( $user->getEmail() ) ;

            $mailer->setSubject("Mire welcome") ;

            $mailer->setContentHTML(
                $this->renderView(
                    'emails/welcome/after-validate.html.twig' ,
                    [ "user" => $user ]
                )
            ) ;

            $mailer->setContentText(
                $this->renderView(
                    'emails/welcome/after-validate.txt.twig' ,
                    [ "user" => $user ]
                )
            ) ;

            $mailer->send( Email::PRIORITY_LOW ) ;

            return $this->redirectToRoute('app_main_index') ;
        }

    }
}
