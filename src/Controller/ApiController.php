<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController {

    protected $serializer ;

    public function __construct() {

        $encoders = [ new XmlEncoder() , new JsonEncoder() ] ;
        $normalizers = [ new DateTimeNormalizer() , new ObjectNormalizer(
            null,null,null,null,null,null,[

                // persist serialize with object depth
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER  => function($object,$format,$context) {
                    return $object ;
                } ,

                // normalize only public attributes
                AbstractNormalizer::ATTRIBUTES => ['title','slug','id','content','createAt','user' ,'username' , 'commentaries']
            ]
        ) ] ;

        $this->serializer = new Serializer( $normalizers , $encoders ) ;
    }

    /**
     * @Route("/api/article/{id}" , methods={"GET"} , name="app_api_article" )
     */
    public function getOneArticle(
        int $id ,
        ArticleRepository $articleRep
    ) {

        $article = $articleRep->find( $id ) ;

        if(
            $article &&
            !$article->getIsRemove() &&
            $article->getIsPublic()
        ) {

            $articleJSON = $this->serializer->serialize(
                $article , 'json'
            ) ;

            return $this->json( [
                "success" => true ,
                "article" => $articleJSON
            ] ) ;

        } else {

            return $this->json( [
                "success" => false ,
                "status" => "not found" ,
                "code" => 404
            ] ) ;
        }

    }

    /**
     * @Route("/api/last-articles/{many}" , methods={"GET"} , name="app_api_last_articles" )
     */
    public function lastArticles( ?int $many = 3 , ArticleRepository $articleRep ) {

        $lastArticles = $articleRep->getLastPublish( $many ) ;

        $lastArticlesJSON = [] ;

        foreach( $lastArticles as $article ) {

            $lastArticlesJSON[] = $this->serializer->serialize(
                $article , 'json'
            ) ;
        }

        return $this->json([
            "count" => \count( $lastArticlesJSON ) ,
            "articles" => $lastArticlesJSON ,
            "success" => true
        ] ) ;
    }

    /**
     * @Route("/api/site-map" , methods={"GET"} , name="api_site_map" )
     */
    public function siteMap( RouterInterface $router ) {

        $isLogged = $this->getUser() != NULL ;
        $routes = $router->getRouteCollection() ;

        // home
        // /a/{page}
        // /contact
        // /about

        // if logged
            // /u/{username}
            // /u/box
            // /u/my/settings
            // /u/my/articles
            // /u/my/subjects
        // else
            // /login
            // /register

        $routesJSON = [] ;

        foreach( $routes as $name => $route ) {

            if(
                !\preg_match(
                    "#(profiler|wdt$|error$|api|admin|activate|site_map|delete|app_main_search|app_message_mark_as_read|app_recaptcha|app_logout|app_user_block|app_is_valid_username|app_user_count_messages|app_article_details|app_message_|app_user_index)#"
                    , $name
                )
            ) {

                if( $isLogged ) {

                    if( !\preg_match( "#(app_login|app_register)#" , $name ) ) {

                        $routesJSON[] = [
                            "name" => $name ,
                            "path" => $route->getPath()
                        ] ;

                    }

                } else {

                    if( !\preg_match("#(app_user|app_article_new|app_message)#" , $name ) ) {

                        $routesJSON[] = [
                            "name" => $name ,
                            "path" => $route->getPath()
                        ] ;
                    }

                }

            }
        }

        return $this->json( [
            "routes" => $routesJSON ,
            "success" => true
        ] ) ;
    }

}
