<?php

namespace khaledBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/shop",name="shop")sss
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $p = $repo->findAll();
        return $this->render('khaledBundle:Shop:index.html.twig',[
            'products' => $p
        ]);
    }

    /**
     * @Route("/shop/{id}")
     */
    public function find($id)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $p = $repo->find($id);
        return $this->render('khaledBundle:Shop:show_product.html.twig',[
            'product' => $p
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function Admin()
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(  ! $hasAccess)
        { return $this->render('khaledBundle:Admin:404.html.twig');}
        else
            { return $this->render('khaledBundle:Admin:index.html.twig'); }


    }
    /**
     * @Route("/404", name="404")
     */
    public function error()
    {

         return $this->render('khaledBundle:Admin:404.html.twig');


    }
}
