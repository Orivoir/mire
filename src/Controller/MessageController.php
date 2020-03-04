<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/message/{id}", name="app_message_open")
     */
    public function open( int $id , MessageRepository $messageRep )
    {

        $message = $messageRep->find( $id ) ;

        if(
            !$message ||
            $message->getBox()->getId() !== $this->getUser()->getMessageBox()->getId() ||
            $message->getIsRemove()
        ) {

            // message open not exists
            // redirect to message list because
            // can remove message and refresh page
            return $this->redirectToRoute('app_user_message_box' , [] , 302 ) ;

        } else {

            // open message authorize

            if( !$message->getIsRead() ) {

                // mark message as read
                $em = $this->getDoctrine()->getManager() ;

                $message->setIsRead( true ) ;

                $em->persist( $message ) ;
                $em->flush() ;
            }

            return $this->render('message/open.html.twig' , [
                "message" => $message
            ] ) ;
        }
    }

    /**
     * @Route("/message/{id}/is-read" , methods={"GET"} , name="app_message_mark_as_read" )
     */
    public function markAsRead(
        int $id ,
        MessageRepository $messageRep
    ) {

        $message = $messageRep->find( $id ) ;

        if(
            !$message ||
            $message->getIsRead()
        ) {

            return $this->json( [
                "success" => false
            ] ) ;

        } else {

            $em = $this->getDoctrine()->getManager() ;

            $message->setIsRead( true ) ;

            $em->persist( $message ) ;
            $em->flush() ;

            return $this->json( [
                "success" => true
            ] ) ;
        }

    }

    /**
     * @Route("/message/{token}/{id}" , methods={"DELETE"} , name="app_message_delete" )
     */
    public function delete(
        string $token ,
        int $id ,
        UserRepository $userRep ,
        MessageRepository $messageRep
    ) {

        $user = $userRep->findOneBy( [
            'token' => $token
        ] ) ;

        $message = $messageRep->find( $id ) ;

        if(
            !$user ||
            $user->getToken() != $this->getUser()->getToken() ||
            !$message ||
            $message->getIsRemove()
        ) {

            // 404
            return $this->json([
                'success' => false
            ]) ;

        } else {

            $message->setIsRemove( true ) ;

            $em = $this->getDoctrine()->getManager() ;

            $em->persist( $message ) ;
            $em->flush() ;

            return $this->json([
                'success' => true
            ]) ;
        }

    }

    /**
     * @Route("/message/response/{target}" , methods={"GET","POST"} , name="app_message_response" )
     */
    public function response( string $target , UserRepository $userRep , Request $rq ) {

        $user = $userRep->find( $target ) ;

        if(
            !$user ||
            $user->getIsRemove()
        ) {

            // 404
            return $this->render('not-found.html.twig') ;

        } else {

            $message = new Message() ;
            $messageForm = $this->createForm( MessageFormType::class , $message ) ;

            $messageForm->handleRequest( $rq ) ;

            if( $messageForm->isSubmitted() && $messageForm->isValid() ) {

                $em = $this->getDoctrine()->getManager() ;

                $em->persist( $message ) ;

                $em->flush() ;

                // @TODO: email here
            }
        }

    }
}
