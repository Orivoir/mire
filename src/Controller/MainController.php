<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{

    /**
     * @Route("/", name="app_main_index")
     */
    public function index( ArticleRepository $articleRep ) {

        $articles = $articleRep->getLastPublish( 3 ) ;

        return $this->render('main/index.html.twig' , [
            "articles" => $articles
        ] );
    }

    /**
     * @Route("/site-map" , methods={"GET"} , name="app_main_site_map" )
     */
    public function siteMap() {

        return $this->render('main/site-map.html.twig') ;

    }

    /**
     * @Route("/contact" , methods={"GET","POST"} , name="app_main_contact" )
     */
    public function contact( Request $rq ) {

        if( $rq->getMethod() === 'POST' && $_POST['g-score'] < .5 ) {
            // probably not humain user
            // reject sign up action
            $this->addFlash('error' , 'captcha have catch contact action' ) ;
            return $this->redirectToRoute('app_main_index') ;
        }

        $contact = new Contact() ;
        $contactForm = $this->createForm( ContactFormType::class , $contact ) ;

        $contactForm->handleRequest( $rq ) ;

        if( $contactForm->isSubmitted() && $contactForm->isValid() ) {

            $em = $this->getDoctrine()->getManager() ;

            $contact->setUser( $this->getUser() ?? NULL ) ;

            $em->persist( $contact ) ;
            $em->flush() ;

            $this->addFlash('success' , 'message send with success') ;
            // @TODO: mail
        }

        return $this->render('main/contact.html.twig' , [
            'contactForm' => $contactForm->createView()
        ] ) ;

    }

    /**
     * @Route("/about" , methods={"GET"} , name="app_main_about" )
     */
    public function about() {

        // :-)
    }

    /**
     * @Route("/search/{searchstring}" , methods={"GET"} , name="app_main_search" )
     */
    public function search(
        $searchstring ,
        UserRepository $userRep ,
        ArticleRepository $articleRep
    ) {

        $searchstringParse = \preg_replace( '/ /','%', $searchstring) ;
        $articles = $articleRep->getAllVisibleSearch( $searchstringParse ) ;

        $backJSON = [
            "@ressource" =>  "/search/". \preg_replace( '/ /' , '%20' , $searchstring ) ,
            "articles-id" => $articles ,
            "search" => '%'.$searchstringParse.'%' ,
            "search-brut" => $searchstring ,
        ] ;

        if( $this->getUser() ) {

            $users = $userRep->getAllVisibleSearch( $searchstring ) ;

            $backJSON['users-id'] = $users ;
        }

        return $this->json( $backJSON ) ;
    }
}
