<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    const ADMIN_USERNAME = "Orivoir21" ;

    /**
     * @Route("/admin", name="app_admin_index")
     */
    public function index() {

        $username = $this->getUser()->getUsername() ;

        if( $username != self::ADMIN_USERNAME  ) {

            return $this->render('not-found.html.twig') ;

        } else {

            return $this->render('admin/index.html.twig');
        }
    }
}
