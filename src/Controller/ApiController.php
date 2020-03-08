<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\Serializer\Serializer;
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
            "succcess" => true
        ] ) ;
    }

}
