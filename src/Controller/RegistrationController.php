<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Recaptcha;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/recaptcha" , methods={"POST"} , name="app_recaptcha" )
     */
    public function recap( Request $rq ) {

        $reca = new Recaptcha(
            $_SERVER['REMOTE_ADDR'] ,
            '6LdSsdwUAAAAALuYufEEYiefMftv-0qfrY0ieSA0'
        ) ;

        $content = $reca->execute( $_POST['token'] ) ;

        return $this->json( $content ) ;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {

        // user already logged
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_index');
        }

        if( $request->getMethod() === 'POST' && $_POST['g-score'] < .5 ) {
            // probably not humain user
            // reject sign up action
            $this->addFlash('error' , 'captcha have catch sign up action' ) ;

            return $this->redirectToRoute('app_main_index') ;

        } else {
            // captcha ok

            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                ) ;
            }

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
