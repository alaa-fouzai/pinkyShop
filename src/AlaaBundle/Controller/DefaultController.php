<?php

namespace AlaaBundle\Controller;

use AlaaBundle\Entity\Cart;
use khaledBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/",name="index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('khaledBundle:Product')->findAll();

        return $this->render('AlaaBundle:Default:index.html.twig', array(
            'products' => $products,
        ));
    }
    /**
     * @Route("/test",name="test")
     */
    public function cartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('khaledBundle:Product')->findAll();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $jsonData = array();
            $idx = 0;
            foreach($products as $product) {
                $temp = array(
                    'name' => $product->getTitle(),
                    'address' => $product->getState(),

                );

                $jsonData[$idx++] = $temp;
            }

            return new JsonResponse($jsonData);
        } else {
            return $this->render('AlaaBundle:Default:test.html.twig', array(
            'products' => $products,
        ));

        }
    }

    /**
     * @Route("/checkout",name="checkout")
     */
    public function checkoutAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (! $user->getId())
        {
            return $this->redirectToRoute('fos_user_security_login');
        }
        //SELECT * FROM fos_user f, product p, cart c WHERE f.id =2 AND p.id =c.IdProduct AND f.id=c.idUser
        //SELECT p.id,p.image,p.price,p.state,p.title FROM fos_user f, product p, cart c WHERE f.id =2 AND p.id =c.IdProduct AND f.id=c.idUser
        $query = $em->createQuery(
                    'SELECT c.id,p.price,p.image,p.title,p.state,p.promotion,c.delivrer FROM AppBundle:User f, khaledBundle:Product p, AlaaBundle:Cart c WHERE f.id =:idUser AND p.id =c.idProduct AND f.id=c.idUser'
        )->setParameter('idUser', $user->getid());
        $produit = $query->getResult();
        //$produit=$em->getRepository('khaledBundle:Product')->findBy(['id' => $cart->getidProduct()]);
        //var_dump($produit);
        $p=new Product();
        $total=0;
        foreach ( $produit as $p)
        {
            if ($p['promotion']>0)
            $total=$total+($p['price']-(($p['price']*$p['promotion'])/100));
            else
                $total=$total+($p['price']);

        }
            return $this->render('AlaaBundle:Default:cart.html.twig', array(
                'cart' => $produit,'total'=>$total
            ));


    }

    /**
     * @Route("/add",name="addToCart")
     */
    public function add_cartAction(Request $request)
    {

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $id = $request->query->get("field1");
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if (! $user->getId())
            {
                return $this->redirectToRoute('fos_user_security_login');
            }
            $cart=new Cart();
            $cart->setIdProduct($id);
            $cart->setDateAchat(new \DateTime("now"));
            $cart->setDelivrer(false);
            $cart->setIdUser($user->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);
            $em->flush();
            if (! $user->getId())
            {
                return $this->redirectToRoute('fos_user_security_login');
            }

            return new JsonResponse("this item is added to your cart");
        } else {
            return new JsonResponse("Failed For Some Reason");
        }
    }
    /**
     * @Route("/remouve",name="remouve_cart")
     */
    public function remouve_cartAction(Request $request)
    {

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $id = $request->query->get("resource");
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if (! $user->getId())
            {
                return $this->redirectToRoute('fos_user_security_login');
            }

            if (! $user->getId())
            {
                return $this->redirectToRoute('fos_user_security_login');
            }

            $em = $this->getDoctrine()->getManager();
            $cart = $em->getRepository('AlaaBundle:Cart')->find($id);
            $em->remove($cart);
            $em->flush();
            return new JsonResponse("Item is removed From you Cart");
        } else {
            return new JsonResponse("Failed For Some Reason");
        }
    }

    /**
     * @Route("/users",name="users")
     */
    public function Users_Action(Request $request)
    {

        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(  ! $hasAccess)
        { return $this->render('khaledBundle:Admin:404.html.twig');}
        else
        {

            if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
                $id = $request->query->get("id");
                $action = $request->query->get("action");
                $userManager = $this->get('fos_user.user_manager');

                // Use findUserby, findUserByUsername() findUserByEmail() findUserByUsernameOrEmail, findUserByConfirmationToken($token) or findUsers()
                $user = $userManager->findUserBy(['id' => $id]);

                // Add the role that you want !
                if ($action=="promote")
                {$user->addRole("ROLE_ADMIN");
                    // Update user roles
                    $userManager->updateUser($user);

                    return new JsonResponse("User Promoted");
                }
                if ($action=="demote")
                {//$role=[a,0,\{\} ];
                    $user->setRoles(array('ROLE_USER'));
                    // Update user roles
                    $userManager->updateUser($user);

                    return new JsonResponse("User Demoted");
                }
                if ($action=="disable")
                {   $user->setEnabled(false);
                    // Update user roles
                    $userManager->updateUser($user);

                    return new JsonResponse("User Disabled");
                }
                if ($action=="enable")
                {   $user->setEnabled(True);
                    // Update user roles
                    $userManager->updateUser($user);

                    return new JsonResponse("User Enabled");
                }
                return new JsonResponse("Error");


            }

            $em = $this->getDoctrine()->getManager();

            $users = $em->getRepository('AppBundle:User')->findAll();

            return $this->render('AlaaBundle:Default:UsersList.html.twig', array(
                'users' => $users,
            ));
        }
    }

    /**
     * @Route("/Admin/orders",name="orders")
     */
    public function orders_Action(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(  ! $hasAccess)
        { return $this->render('khaledBundle:Admin:404.html.twig');}
        else
        {
            $em = $this->getDoctrine()->getManager();
            //SELECT c.id,p.price,p.image,p.title,p.state,c.deliverd FROM fos_user f, Product p, orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser
            $query = $em->createQuery(
                'SELECT c.id,f.username,p.price,p.image,p.title,p.state,c.deliverd,c.orderDate FROM AppBundle:User f, khaledBundle:Product p, AlaaBundle:Orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser'
            );
            $orders = $query->getResult();



            //var_dump($orders);


            return $this->render('AlaaBundle:Default:All_Orders.html.twig', array(
                'orders' => $orders,
            ));
        }
    }
    /**
     * @Route("/Admin/new_orders",name="new_orders")
     */
    public function new_orders_Action(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(  ! $hasAccess)
        { return $this->render('khaledBundle:Admin:404.html.twig');}
        else
        {

            if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
                $id = $request->query->get("id");
                $action = $request->query->get("action");

                // Add the role that you want !
                if ($action=="done")
                {
                    $em = $this->getDoctrine()->getManager();

                    $order = $em->getRepository('AlaaBundle:Orders')->find($id);
                    //$order->setdeliverd(true);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($order);
                    $em->flush();

                    return new Response($id);
                }
                return new JsonResponse("Error");

            }


            $em = $this->getDoctrine()->getManager();
            //SELECT c.id,p.price,p.image,p.title,p.state,c.deliverd FROM fos_user f, Product p, orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser
            $query = $em->createQuery(
                'SELECT c.id,f.username,p.price,p.image,p.title,p.state,c.deliverd,c.orderDate FROM AppBundle:User f, khaledBundle:Product p, AlaaBundle:Orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser and c.deliverd=False order by c.id DESC'
            );
            $orders = $query->getResult();
            return $this->render('AlaaBundle:Default:New_Orders.html.twig', array(
                'orders' => $orders,
            ));
        }
    }

    /**
     * @Route("/Admin/Remise",name="Remise")
     */
    public function Remise_Action(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(  ! $hasAccess)
        { return $this->render('khaledBundle:Admin:404.html.twig');}
        else
        {
            /*$em = $this->getDoctrine()->getManager();
            //SELECT c.id,p.price,p.image,p.title,p.state,c.deliverd FROM fos_user f, Product p, orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser
            $query = $em->createQuery(
                'SELECT c.id,f.username,p.price,p.image,p.title,p.state,c.deliverd,c.orderDate FROM AppBundle:User f, khaledBundle:Product p, AlaaBundle:Orders c WHERE f.id =c.idUser AND p.id =c.idProduct AND f.id=c.idUser'
            );
            $orders = $query->getResult();

        */

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT DISTINCT(p.category) FROM khaledBundle:Product p'
            );
            $orders = $query->getResult();

            if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
                $action = $request->query->get("action");
                $remise = $request->query->get("remise");
                $categorie = $request->query->get("categorie");
                $signal = $request->query->get("signal");

                if ($action=="all" )
                {
                    $em = $this->getDoctrine()->getManager();

                    $products = $em->getRepository('khaledBundle:Product')->findAll();
                    foreach ($products as $produit)
                    {
                        $produit->setPromotion($remise);
                        $em->persist($produit);
                        $em->flush();

                    }

                    return new Response("Sucess");
                }
                if ($action=="categorie" AND $signal=="not done")
                {
                    /*$em = $this->getDoctrine()->getManager();

                    $products = $em->getRepository('khaledBundle:Product')->findBy(array('category' => $action));
                    foreach ($products as $produit)
                    {
                        $produit->setPromotion($remise);
                        $em->persist($produit);
                        $em->flush();

                    }
                    */
                    //SELECT DISTINCT(category) FROM `product`
                    $em = $this->getDoctrine()->getManager();
                    $query = $em->createQuery(
                        'SELECT DISTINCT(p.category) FROM khaledBundle:Product p'
                    );
                    $orders = $query->getResult();
                    $html="";
                    foreach ($orders as $c)
                    {
                        $html.="<input type=\"checkbox\" name=\"sport\" value=".$c[1].">".$c[1]."<br>";
                    }

                    return new Response($html);
                }
                if ($action=="categorie" AND $signal=="done")
                {  //var_dump($categorie);
                    //die();

                    foreach ($categorie as $c)
                    {
                        $em = $this->getDoctrine()->getManager();

                        $products = $em->getRepository('khaledBundle:Product')->findBy(array('category' => $c));
                        foreach ($products as $produit)
                        {
                            $produit->setPromotion($remise);
                            $em->persist($produit);
                            $em->flush();

                        }
                    }
                    return new Response("success");
                }


                if ($action=="produit" AND $signal=="not done")
                {
                    $em = $this->getDoctrine()->getManager();
                    $query = $em->createQuery(
                        'SELECT DISTINCT p.id,p.title FROM khaledBundle:Product p WHERE p.state > 1'
                    );
                    $orders = $query->getResult();
                    $html="";
                    foreach ($orders as $c)
                    {
                        $html.="<input type=\"checkbox\" name=\"sport\" value=".$c["id"].">".$c["title"]."<br>";
                    }

                    return new Response($html);
                }
                if ($action=="produit" AND $signal=="done")
                {  //var_dump($categorie);
                    //die();

                    foreach ($categorie as $c)
                    {
                        $em = $this->getDoctrine()->getManager();

                        $products = $em->getRepository('khaledBundle:Product')->findBy(array('id' => $c));
                        foreach ($products as $produit)
                        {
                            $produit->setPromotion($remise);
                            $em->persist($produit);
                            $em->flush();

                        }
                    }
                    return new Response("Success");
                }
                if ( $categorie!="")
                {

                    return new Response("categorie");
                }





                return new JsonResponse($action);

            }


            return $this->render('AlaaBundle:Default:Remise.html.twig', array(
                'orders' => $orders,
            ));
        }
    }





}
