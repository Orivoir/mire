<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentary;
use Doctrine\ORM\EntityManager;
use App\Form\CommentaryFormType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    /**
     * @Route("/article", name="app_article_index")
     */
    public function index( ArticleRepository $articleRep ) {

        $articles = $articleRep->getLastPublish( 3 ) ;

        return $this->render('article/index.html.twig' , [
            "articles" => $articles
        ] ) ;
    }

    /**
     * @Route("/a/{slug}/{id}" , name="app_article_details" )
     */
    public function details( string $slug , int $id , ArticleRepository $articleRep , Request $rq ) {

        $article = $articleRep->find( $id ) ;

        if( !$article || $article->getIsRemove() ) {
            // 404
            return $this->render('not-found.html.twig') ;
        } else {

            if(
                !$article->getIsPublic() &&
                $this->getUser() &&
                $this->getUser()->getId() !== $article->getUser()->getId()
            ) {
                // 403 forbidden mute with 404
                return $this->render('not-found.html.twig') ;

            } else {

                $realSlug = $article->getSlug() ;

                if( $realSlug !== $slug ) {

                    // redirect to symbolic link
                    return $this->redirectToRoute( 'app_article_details' , [
                        "slug" => $realSlug ,
                        "id" => $id
                    ] , 302 ) ;
                } else {

                    $commentaries = $article->getCommentariesVisible() ;

                    $commentary = new Commentary() ;
                    $commentaryForm = $this->createForm( CommentaryFormType::class , $commentary ) ;

                    $commentaryForm->handleRequest( $rq ) ;

                    if( $commentaryForm->isSubmitted() && $commentaryForm->isValid() ) {

                        $em = $this->getDoctrine()->getManager() ;

                        $commentary
                            ->setUser( $this->getUser() )
                            ->setArticle( $article )
                        ;

                        $em->persist( $commentary ) ;
                        $em ->flush() ;

                        // @TODO: send mail here
                    }

                    return $this->render('article/details.html.twig' , [
                        "article" => $article ,
                        "commentaries" => $commentaries ,
                        "commentaryForm" => $commentaryForm->createView()
                    ] ) ;
                }

            }

        }
    }

    /**
     * @Route("/article/new" , name="app_article_new" )
     */
    public function new( Request $rq  ) {

        if( !$this->getUser()->getIsValid() ) {

            // here user have not valid account
            // not access write article
            return $this->render('not-valid.html.twig' , [
                "user" => $this->getUser()
            ] ) ;

        } else {

            $article = new Article() ;

            $form = $this->createForm( ArticleType::class , $article ) ;

            $form->handleRequest( $rq ) ;

            if( $form->isSubmitted() && $form->isValid() ) {

                $em = $this->getDoctrine()->getManager() ;

                $article->setUser( $this->getUser() ) ;

                $em->persist( $article ) ;

                $em->flush() ;

                // email

                return $this->redirectToRoute('app_article_details' , [
                    "slug" => $article->getSlug() ,
                    "id" => $article->getId()
                ] , 302 ) ;
            }

            return $this->render('article/new.html.twig' , [
                "articleForm" => $form->createView()
            ] ) ;

        }

    }

    /**
     * @Route("/article/:token/:id" , methods={"DELETE"} , name="app_article_delete" )
     */
    public function delete(
        string $token ,
        int $id ,
        ArticleRepository $articleRep
    ) {

        $backJSON = [] ;

        if( $this->getUser()->getToken() !== $token ) {

            // invalid token user
            // delete reject

            // bad request
            $backJSON['code'] = 400 ;
            $backJSON['details'] = "invalid user token";

        } else {

            $article = $articleRep->find( $id ) ;

            if( !$article || $article->getIsRemove() ) {

                // 404 , not exists or already remove
                $backJSON['code'] = 404 ;
                $backJSON['details'] = "article not exists";

            } else if(
                // currently user connect is not author of article ask delete
                $this->getUser()->getId() !== $article->getUser()->getId()
            ) {

                // forbidden but return 404 mute
                // user have not right delete

                // here an user have try execute an action with an other
                // token user .
                $backJSON['code'] = 404 ;
                $backJSON['details'] = "article not exists";

            } else {
                // here action delete is accept

                $em = $this->getDoctrine()->getManager() ;

                $article->setIsRemove( true ) ;

                $em->persist( $article ) ;

                $em->flush() ;

                // @TODO: mailer here
            }

        }

        return $this->json( $backJSON ) ;
    }

}
