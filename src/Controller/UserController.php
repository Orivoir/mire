<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Services\SendEmail;
use App\Form\MessageFormType;
use App\Form\SettingsFormType;
use App\Services\FileUploader;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/u", name="app_user_index")
     */
    public function index() {

        return $this->redirectToRoute('app_user_profil' , [
            'username' => $this->getUser()->getUsername()
        ] ) ;
    }

    /**
     * @Route("/u/{username}" , methods={"GET","POST"} , name="app_user_profil" )
     */
    public function profil( string $username , UserRepository $userRep , Request $rq ) {

        $message = new Message() ;
        $formMessage = $this->createForm( MessageFormType::class , $message ) ;

        $formMessage->handleRequest( $rq ) ;

        $user = $userRep->findOneBy([
            "username" => $username
        ] ) ;


        if( !$user || $user->getIsRemove() ) {

            // 404 , user not exists
            return $this->render('not-found.html.twig') ;

        } else {

            if(
                // because cant send message with: from === to
                $user->getId() != $this->getUser()->getId() &&
                $formMessage->isSubmitted() &&
                $formMessage->isValid()
            ) {

                $message
                    ->setBox( $user->getMessageBox() )
                    ->setAuthor( $this->getUser() )
                ;

                $em = $this->getDoctrine()->getManager() ;

                $em->persist( $message ) ;
                $em->flush() ;
            }

            if(
                $this->getUser()->getId() !== $user->getId() &&
                !$user->getIsPublicProfil()
            ) {

                // 200 success
                // but show static reply document because private profil
                return $this->render('user/profilPrivate.html.twig' , [
                    "visit" => $this->getUser() ,
                    "host" => $user ,
                    "formMessage" => $formMessage->createView()
                ] ) ;

            } else if( $user->getId() == $this->getUser()->getId() ) {

                // 200 success
                // and host user === visit user
                // show custom profil tools
                // cant send message
                return $this->render('user/profilAuthorize.html.twig' , [
                    "visit" => null ,
                    "host" => $user
                ] ) ;

            } else {

                // 200 success
                // profil is public and visit user !== host user
                return $this->render('user/profil.html.twig' , [
                    "visit" => $this->getUser() ,
                    "host" => $user ,
                    "formMessage" => $formMessage->createView()
                ] ) ;
            }

        }
    }

    /**
     * @Route("/u/count/new-messages" , methods={"GET"} , name="app_user_count_messages" )
     */
    public function messagesCount() {

        $messageBox = $this->getUser()->getMessageBox() ;

        $newMessages = $messageBox->getNewMessages() ;

        return $this->json( [
            "count" => $newMessages->count()
        ] ) ;
    }

    /**
     * @Route("/u/box/messages" , methods={"GET"} , name="app_user_message_box" )
     */
    public function messageBox() {

        $messageBox = $this->getUser()->getMessageBox() ;

        $messagesReceveid = $messageBox->getMessagesVisible() ;

        return $this->render('user/message-box.html.twig' , [
            'messages' => $messagesReceveid
        ] ) ;
    }

    /**
     * @Route("/u/block/{id}" , methods={"BLOCK"} , name="app_user_block" )
     */
    public function block(
        int $id ,
        UserRepository $userRep
    ) {

        $user2block = $userRep->find( $id ) ;

        if(
            !$user2block ||

            // cant block an remove user
            $user2block->getIsRemove() ||

            // cant auto block
            $user2block->getId() == $this->getUser()->getId()
        ) {
            return $this->json([
                'success' => false
            ]) ;

        } else {

            $blockers = $this->getUser()->getBlockers() ;
            $blockers[] = $user2block->getId() ;

            if( $this->getUser()->isBlocked( $user2block ) ) {

                // user already blocked
                return $this->json([
                    'success' => true
                ]) ;

            } else {

                $this->getUser()->setBlockers( $blockers ) ;

                $em = $this->getDoctrine()->getManager() ;

                $em->persist( $this->getUser() ) ;

                $em->flush() ;

                // @TODO: mail here

                return $this->json([
                    'success' => true
                ]) ;
            }
        }
    }

    /**
     * @Route("/u/my/settings" , methods={"GET", "POST"} , name="app_user_settings" )
     */
    public function settings( Request $rq ) {

        $user = $this->getUser() ;
        $lastEmail = $user->getEmail() ;

        $settingsForm = $this->createForm( SettingsFormType::class , $user ) ;

        $settingsForm->handleRequest( $rq ) ;

        if( $settingsForm->isSubmitted() && $settingsForm->isValid() ) {

            $em = $this->getDoctrine()->getManager() ;

            if( $user->getEmail() ) {
                // if user have give an email

                if( !$user->getIsValid() ) {

                    $this->addFlash(
                        'success' ,
                        'check you mailbox activate account link'
                    ) ;

                    $mailer = new SendEmail( $user->getEmail() ) ;

                    $mailer->setSubject("Mire activate account") ;

                    $mailer->setContentHTML(
                        $this->renderView(
                            'emails/validate/validate-account.html.twig' ,
                            [ "user" => $user ]
                        )
                    ) ;

                    $mailer->setContentText(
                        $this->renderView(
                            'emails/validate/validate-account.txt.twig'
                            , [ "user" => $user ]
                        )
                    ) ;

                    $mailer->send( Email::PRIORITY_HIGH ) ;
                } else {
                    // if user already validate account
                    // reject an change email
                    // the field emails is hide inner render twig
                    // but not protect send data
                    $user->setEmail( $lastEmail ) ;
                }
            }

            $avatar = $settingsForm->get('avatar')->getData() ;

            if( $avatar ) {

                $fileUp = new FileUploader( $this->getParameter('uploads_avatar') ) ;

                if( !!$user->getAvatarName() ) {

                    $fileUp->remove(
                        $this->getParameter('uploads_avatar')
                        . '/' .
                        $user->getAvatarName()
                    ) ;
                }

                $status = $fileUp->upload( $avatar ) ;

                if( $status ) {
                    $user->setAvatarName( $status ) ;
                } else {

                    // @TODO: implements an logger interface
                    $this->addFlash('error' , 'cant change avatar') ;
                }
            }

            $em->persist( $user ) ;
            $em->flush() ;

            $this->addFlash('success' , 'settings has been applicate' ) ;

            // @TODO: mail
        }

        return $this->render('user/settings.html.twig' , [
            "user" => $user ,
            "settingsForm" => $settingsForm->createView()
        ] ) ;
    }

    /**
     * @Route("/u/remove/{token}" , methods={"DELETE"} , name="app_user_delete" )
     */
    public function delete( string $token  , UserRepository $userRep ) {

        $user = $userRep->findOneBy( [
            "token" => $token
        ] ) ;

        $backJsonNotFound = [
            "success" => false ,
            "status" => "not found" ,
            "code" => 404
        ] ;

        if( !$user || $user->getIsRemove() ) {

            //  404
            $backJsonNotFound["not-exists"] = true ;
            return $this->json( $backJsonNotFound ) ;

        } else if( $user->getId() !== $this->getUser()->getId() ) {

            // forbidden mute , 404
            // user have not right delete
            $backJsonNotFound["username"] = [$user->getUsername() , $this->getUser()->getUsername()] ;
            return $this->json( $backJsonNotFound ) ;

        } else {

            $user->setIsRemove( true ) ;

            if( $user->getAvatarName() != NULL ) {

                // remove avatar from : "uploads_avatar" directory

                $uploadsAvatarDirectory = $this->getParameter( "uploads_avatar" ) ;

                $filepath = $uploadsAvatarDirectory . '/' . $user->getAvatarName() ;

                $isRemoveAvatar = \unlink( $filepath ) ;

                // if remove avatar file have fail
                if( !$isRemoveAvatar ) {
                    // @TODO: implement logger
                }
            }

            $em = $this->getDoctrine()->getManager() ;

            $em->persist( $user ) ;
            $em->flush() ;

            $this->addFlash('success' , 'account remove with success' ) ;

            //  @TODO: mailer

            return $this->redirectToRoute('app_logout') ;
        }
    }

    /**
     * @Route("/u/my/articles" , methods={"GET"} , name="app_user_aticles" )
     */
    public function myArticles() {

        $articles = $this->getUser()->getArticles() ;

        return $this->render('user/my-articles.html.twig' , [
            "user" => $this->getUser() ,
            "articles" => $articles
        ] ) ;
    }

    /**
     * @Route("/u/my/subjects" , methods={"GET"} , name="app_user_subjects" )
     */
    public function mySubjects() {

        $user = $this->getUser() ;
        $articlesSubjects = $user->getSubjects() ;

        return $this->render('user/my-subjects.html.twig' , [
            "user" => $user ,
            "articles" => $articlesSubjects
        ] ) ;
    }
}
