<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\ContactRepository;
use App\Repository\CommentaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    const ADMIN_USERNAME = "Orivoir21" ;

    /**
     * @Route("/admin", methods={"GET"} , name="app_admin_index")
     */
    public function index() {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;

        } else {

            return $this->render('admin/index.html.twig') ;
        }
    }

    /**
     * @Route("/admin/users/{page}" , methods={"GET"} , name="app_admin_users")
     */
    public function users(
        int $page = 1 ,
        UserRepository $userRep ,
        PaginatorInterface $paginator ,
        Request $rq
    ) {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;

        }

        $query = $userRep->findAllQuery() ;

        $range = $paginator->paginate(
            $query ,
            $page ?? 1 ,
            5
        ) ;

        return $this->render('admin/users.html.twig' , [
            "range" => $range
        ] ) ;
    }

    /**
     * @Route("/admin/articles/{page}" , methods={"GET"} , name="app_admin_articles")
     */
    public function articles(
        int $page = 1 ,
        ArticleRepository $articleRep ,
        PaginatorInterface $paginator ,
        Request $rq
    ) {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;

        }

        $query = $articleRep->findAllQuery() ;

        $range = $paginator->paginate(
            $query ,
            $page ?? 1 ,
            5
        ) ;

        return $this->render('admin/articles.html.twig' , [
            "range" => $range
        ] ) ;
    }

    /**
     * @Route("/admin/commentaries/{page}" , methods={"GET"} , name="app_admin_commentaries")
     */
    public function commentaries(
        int $page = 1 ,
        CommentaryRepository $commentaryRep ,
        PaginatorInterface $paginator ,
        Request $rq
    ) {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;
        }

        $query = $commentaryRep->findAllQuery() ;

        $range = $paginator->paginate(
            $query ,
            $page ?? 1 ,
            5
        ) ;

        return $this->render('admin/commentaries.html.twig' , [
            "range" => $range
        ] ) ;
    }

    /**
     * @Route("/admin/feedback/{page}" , methods={"GET"} , name="app_admin_feedback")
     */
    public function feedback(
        int $page = 1 ,
        ContactRepository $contactRep ,
        PaginatorInterface $paginator ,
        Request $rq
    ) {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;
        }

        $query = $contactRep->findAllQuery() ;

        $range = $paginator->paginate(
            $query ,
            $page ?? 1 ,
            3
        ) ;

        return $this->render('admin/feedback.html.twig' , [
            "range" => $range
        ] ) ;
    }


}
