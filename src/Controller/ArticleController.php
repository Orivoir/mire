<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentary;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManager;
use App\Form\CommentaryFormType;
use App\Form\SettingsArticleFormType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    /**
     * @Route("/a/{page}", name="app_article_index")
     */
    public function index(
        ?int $page = 1,
        ArticleRepository $articleRep ,
        PaginatorInterface $paginator ,
        Request $rq
    ) {

        $queryArticles = $articleRep->getAllVisibleQuery( 500 ) ;

        $maxArticlesByPage = 5 ;

        $range = $paginator->paginate(
            $queryArticles ,
            $page ?? 1 ,
            $maxArticlesByPage ,
        ) ;

        return $this->render('article/index.html.twig' , [
            "articles" => $range
        ] ) ;
    }

    /**
     * @Route("/article/settings/{slug}/{id}" , name="app_article_settings" )
     */
    public function settings(
        string $slug ,
        int $id ,
        ArticleRepository $articleRep ,
        Request $rq
    ) {

        $article = $articleRep->find( $id ) ;

        if( !$article || $article->getIsRemove() ) {

            return $this->render('not-found.html.twig') ;

        } else if(
            $article->getUser()->getId() !== $this->getUser()->getId()
        ) {

            return $this->render('not-found.html.twig') ;
        }

        $articleSettingsForm = $this->createForm( SettingsArticleFormType::class , $article ) ;

        $articleSettingsForm->handleRequest( $rq ) ;

        if( $articleSettingsForm->isSubmitted() && $articleSettingsForm->isValid() ) {

            $background = $articleSettingsForm->get('background')->getData() ;

            if( $background ) {

                // remove current background if exists
                if( $article->getBackgroundName() != NULL ) {

                    // remove background image from: 'uploads_bg_article' directory

                    $uploadsBackgroundDirectory = $this->getParameter('uploads_bg_article') ;

                    $filepath = $uploadsBackgroundDirectory . '/' . $article->getBackgroundName() ;

                    $isRemoveBackground = \unlink( $filepath ) ;

                    // if remove background have fail
                    if( $isRemoveBackground ) {

                        // @TODO: implement logger interface
                    }
                }

                $fileUp = new FileUploader( $this->getParameter('uploads_bg_article') ) ;

                $filename = $fileUp->upload( $background ) ;

                if( !!$filename ) {

                    $article->setBackgroundName( $filename ) ;

                } else {

                    // @TODO: implements an logger interface
                    $this->addFlash('error' , 'background article cant be uploads' ) ;
                }
            }

            $em = $this->getDoctrine()->getManager() ;

            $em->persist( $article ) ;

            $em->flush() ;

            $this->addFlash('success' , 'article update success') ;

            return $this->redirectToRoute('app_article_details' , [
                "slug" => $article->getSlug() ,
                "id" => $article->getId()
            ] ) ;
        }

        return $this->render('article/settings.html.twig' , [
            "articleSettingsForm" => $articleSettingsForm->createView()
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

                $background = $form->get('background')->getData() ;

                if( $background ) {

                    $fileUp = new FileUploader( $this->getParameter('uploads_bg_article') ) ;

                    $filename = $fileUp->upload( $background ) ;

                    if( !!$filename ) {

                        $article->setBackgroundName( $filename ) ;

                    } else {

                        // @TODO: implements an logger interface
                        $this->addFlash('error' , 'background article cant be uploads' ) ;
                    }
                }

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
     * @Route("/article/{token}/{id}" , methods={"DELETE"} , name="app_article_delete" )
     */
    public function delete(
        string $token ,
        int $id ,
        ArticleRepository $articleRep
    ) {

        $backJSON = [
            'success' => true
        ] ;

        if( $this->getUser()->getToken() !== $token ) {

            // invalid token user
            // delete reject

            // bad request
            $backJSON['code'] = 400 ;
            $backJSON['success'] = false ;
            $backJSON['details'] = "invalid user token";

        } else {

            $article = $articleRep->find( $id ) ;

            if( !$article || $article->getIsRemove() ) {

                // 404 , not exists or already remove
                $backJSON['code'] = 404 ;
                $backJSON['success'] = false ;
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
                $backJSON['success'] = false ;
                $backJSON['details'] = "article not exists";

            } else {
                // here action delete is accept

                if( $article->getBackgroundName() != NULL ) {

                    // remove background image from: 'uploads_bg_article' directory

                    $uploadsBackgroundDirectory = $this->getParameter('uploads_bg_article') ;

                    $filepath = $uploadsBackgroundDirectory . '/' . $article->getBackgroundName() ;

                    $isRemoveBackground = \unlink( $filepath ) ;

                    // if remove background have fail
                    if( $isRemoveBackground ) {

                        // @TODO: implement logger interface
                    }
                }

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
