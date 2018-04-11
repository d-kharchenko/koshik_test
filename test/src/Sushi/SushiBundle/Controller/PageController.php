<?php
// src/Sushi/SushiBundle/Controller/PageController.php

namespace Sushi\SushiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Import new namespaces
use Symfony\Component\HttpFoundation\Request;
use Sushi\SushiBundle\Entity\Products;
use Sushi\SushiBundle\Form\EnquiryType;
//
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Sushi\SushiBundle\Entity\Orders;
use Sushi\SushiBundle\Entity\OrdersProducts;
use Symfony\Component\Validator\Constraints\DateTime;

class PageController extends Controller
{
    public function indexAction() {
        return $this->render('SushiSushiBundle:Page:index.html.twig');    
    }
    
    public function catalogAction()
    {
        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();

        $groups = $em
                    ->select('gr')
                    ->from('SushiSushiBundle:Groups',  'gr')
                    ->addOrderBy('gr.orderGroup', 'ASC')
                    ->getQuery()
                    ->getResult();
        
        $it = 0;
        foreach ($groups as $curGroup) {
            $curGroupId = $curGroup->getId();
            $products = $this->getDoctrine()
                ->getRepository('SushiSushiBundle:Products')
                ->findByGroupId($curGroupId);
            $groupsProducts[$it] = array('groupId' => $curGroupId, 'groupName' => $curGroup->getName(), 'products' => $products);
            $it++;
        }
        
        /*$products = $em
                    ->select('pr')
                    ->from('SushiSushiBundle:Products',  'pr')
                    ->where('pr.groupId = :groupId')
                    ->addOrderBy('pr.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('groupId', 2)
                    ->getResult();*/

        $products_array_idqty = $this->getProductsArrayFromCookies(0,0);
        
        $products_ids = array_keys($products_array_idqty);

        $products_cart_q = $em
                    ->select('pr_cart')
                    ->from('SushiSushiBundle:Products',  'pr_cart')
                    ->where('pr_cart.id IN (:products_ids)')
                    ->addOrderBy('pr_cart.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getResult();
        
        $it = 0;
        $sumOrderTotal = 0;
        $itemCountTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $CurQty = $products_array_idqty[$curProduct->getId()];
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1);
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
            $itemCountTotal = $itemCountTotal + $CurQty;
            $it++;
        }
        if (count($products_cart_q) === 0)
        {
            $products_cart[0] = array("id"=>0, "name" => "Кошик порожній", "description" => "", "price" => "", "qty" => "", "row_num" => "");
            $products_array_idqty[0] = 0;
        }
        //return $this->json(array('ids'=>$products_array_idqty));
        //var_dump($products_cart);
        $sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);
        return $this->render('SushiSushiBundle:Page:catalog.html.twig', array('groups_products' => $groupsProducts, 'products' => $products, 'products_cart' => $sortedCart, 'sumOrderTotal' => number_format($sumOrderTotal, 2, '.', ''), 'itemCountTotal' => $itemCountTotal, 'isAjax' => true));
    }

    public function carttableAction()
    {
       
        $request = new Request(
        $_GET,
        $_POST,
        array(),
        $_COOKIE,
        $_FILES,
        $_SERVER
        );
        
        //$session = new Session();
        //$session_id = $session->getId();
        
        //$request = new Request();
        //$request = $this->getRequest();

        $cookies = $request->cookies;

        if ($cookies->has('koshik_sushi_cart'))
        {
            $products_array_idqty = unserialize($cookies->get('koshik_sushi_cart'));
        }
        else 
        {
            $products_array_idqty[0] = 0;//"empy cart";           
        }
        
        $products_ids = array_keys($products_array_idqty);
        //$products_basket = $cookies['foo'];
        
        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();
                   
        $products_cart_q = $em
                    ->select('pr_cart')
                    ->from('SushiSushiBundle:Products',  'pr_cart')
                    ->where('pr_cart.id IN (:products_ids)')
                    ->addOrderBy('pr_cart.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getResult();
        
        //var_dump($products_cart_q);
        $it = 0;
        $sumOrderTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $products_array_idqty[$curProduct->getId()], "row_num" => $it+1);
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
            $it++;
        }
        if (count($products_cart_q) === 0)
        {
            $products_cart[0] = array("id"=>0, "name" => "Кошик порожній", "description" => "", "price" => "", "qty" => "", "row_num" => "");
        }

        return $this->render('SushiSushiBundle:Page:carttable.html.twig', array('products_cart' => $products_cart, 'sumOrderTotal' => $sumOrderTotal, 'isAjax' => true));
    }

    public function aboutAction()
    {
        return $this->render('SushiSushiBundle:Page:about.html.twig');
    }
        
    public function AddtoCartAjaxAction($product_id) 
    {
        $products_array_idqty = $this->getProductsArrayFromCookies($product_id, 1);
        $this->saveProductsArrayToCookies($products_array_idqty);

        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();

        $products_ids = array_keys($products_array_idqty);

        $products_cart_q = $em
                    ->select('pr_cart')
                    ->from('SushiSushiBundle:Products',  'pr_cart')
                    ->where('pr_cart.id IN (:products_ids)')
                    ->addOrderBy('pr_cart.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getResult();
        
        $it = 0;
        $sumOrderTotal = 0;
        $itemCountTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $CurQty = $products_array_idqty[$curProduct->getId()];
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1);
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
            $itemCountTotal = $itemCountTotal + $CurQty;
            $it++;
        }
        if (count($products_cart_q) === 0)
        {
            $products_cart[0] = array("id"=>0, "name" => "Кошик порожній", "description" => "", "price" => "", "qty" => "", "row_num" => "");
            $products_array_idqty[0] = 0;
        }
        $sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);
        $response_json = $this->json(array('products_array_idqty' => $products_array_idqty, 'products_cart' => $sortedCart, 'sumOrderTotal' => $sumOrderTotal,'itemCountTotal' => $itemCountTotal, 'isAjax' => true));
        return $response_json;
                
    }

    public function delFromCartAjaxAction($product_id)
    {
        $products_array_idqty = $this->getProductsArrayFromCookies($product_id, -1);
        $this->saveProductsArrayToCookies($products_array_idqty);

        $em = $this->getDoctrine()
        ->getManager()
        ->createQueryBuilder();

        $products_ids = array_keys($products_array_idqty);

        $products_cart_q = $em
         ->select('pr_cart')
         ->from('SushiSushiBundle:Products',  'pr_cart')
         ->where('pr_cart.id IN (:products_ids)')
         ->addOrderBy('pr_cart.orderProduct', 'ASC')
         ->getQuery()
         ->setParameter('products_ids', $products_ids)
         ->getResult();

        $it = 0;
        $sumOrderTotal = 0;
        $itemCountTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $CurQty = $products_array_idqty[$curProduct->getId()];
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1);
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
            $itemCountTotal = $itemCountTotal + $CurQty;
            $it++;
        }
        if (count($products_cart_q) === 0)
        {
            $products_cart[0] = array("id"=>0, "name" => "Кошик порожній", "description" => "", "price" => "", "qty" => "", "row_num" => "");
            $products_array_idqty[0] = 0;
        }

        /*$response = new Response($this->json(array('products_cart' => $products_cart, 'sumOrderTotal' => $sumOrderTotal,'itemCountTotal' => $itemCountTotal, 'isAjax' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;*/
        $sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);
        $responseJson = $this->json(array('products_cart' => $sortedCart, 'sumOrderTotal' => $sumOrderTotal,'itemCountTotal' => $itemCountTotal, 'isAjax' => true));
        return $responseJson;
    }

    public function setCartItemCountAjaxAction($product_id, $qty, $isSet) 
    {
        $products_array_idqty = $this->getProductsArrayFromCookies($product_id, $qty, $isSet);
        $this->saveProductsArrayToCookies($products_array_idqty);

        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();

        $products_ids = array_keys($products_array_idqty);

        $products_cart_q = $em
                    ->select('pr_cart')
                    ->from('SushiSushiBundle:Products',  'pr_cart')
                    ->where('pr_cart.id IN (:products_ids)')
                    ->addOrderBy('pr_cart.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getResult();
        
        $it = 0;
        $sumOrderTotal = 0;
        $itemCountTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $CurQty = $products_array_idqty[$curProduct->getId()];
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1);
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
            $itemCountTotal = $itemCountTotal + $CurQty;
            $it++;
        }
        if (count($products_cart_q) === 0)
        {
            $products_cart[0] = array("id"=>0, "name" => "Кошик порожній", "description" => "", "price" => "", "qty" => "", "row_num" => "");
            $products_array_idqty[0] = 0;
        }
        $sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);
        $response_json = $this->json(array('products_array_idqty' => $products_array_idqty, 'products_cart' => $sortedCart, 'sumOrderTotal' => $sumOrderTotal,'itemCountTotal' => $itemCountTotal, 'isAjax' => true));
        return $response_json;
                
    }

    public function SortArrayByCookie($products_array_idqty, $products_cart)
    {
        /*$it = 0;
        foreach ($products_array_idqty as $curId=>$curQty) {
            $key = array_search($curId, array_column($products_cart, 'id'));
            if ($curId > 0){
                $products_cart[$key]['row_num'] = $it+1;
            }
            if ($it === 0) {
                $sortedCart[$it] = $products_cart[$key];
                $it++;
            } else
            {
                $key_sorted = array_search($curId, array_column($sortedCart, 'id'));
                if ($key_sorted === false){
                    $sortedCart[$it] = $products_cart[$key];
                    $it++;   
                }
            }
        }*/
        $it = 0;
        foreach ($products_array_idqty as $curId=>$curQty) {
            $key = array_search($curId, array_column($products_cart, 'id'));
            if (($key === false) === false){
                if ($curId === 0){
                    $products_cart[$key]['row_num'] = "";
                } else{
                    $products_cart[$key]['row_num'] = $it+1;
                }
                $sortedCart[$it] = $products_cart[$key];
                $it++;
            }
        }
        return $sortedCart;
    }

    public function getProductsArrayFromCookies($product_id, $curQty, $isSet = 0)
    {
        $request = new Request(
            $_GET,
            $_POST,
            array(),
            $_COOKIE,
            $_FILES,
            $_SERVER
            );
            
        $cookies = $request->cookies;
            
        $cookies_cart_name = 'koshik_sushi_cart';
            
        if ($cookies->has($cookies_cart_name))
        {
            $products_array_idqty = unserialize($cookies->get($cookies_cart_name));
               
            if ($product_id > 0)
            {
                if (array_key_exists($product_id, $products_array_idqty))
                {
                    if ((int)$isSet === 1){
                        $products_array_idqty[$product_id] = $curQty;
                    } else {
                        $products_array_idqty[$product_id] = $products_array_idqty[$product_id] + $curQty;
                    }
                }
                else
                {
                    $products_array_idqty[$product_id] = $curQty;
                }
            }
            
        }
        else 
        {
            if ($product_id > 0)
            {
                $products_array_idqty[$product_id] = $curQty;
            }
            else
            {
                $products_array_idqty[0] = 0;//"empy cart;   
            }
        }

        if  (($product_id > 0) and ($products_array_idqty[$product_id] <= 0))
        {
            unset($products_array_idqty[$product_id]);
        }

        return $products_array_idqty;

    }

    public function saveProductsArrayToCookies($products_array_idqty)
    {
        $cookies_cart_name = 'koshik_sushi_cart';
        $response = new Response();
        $response->headers->setCookie(new Cookie($cookies_cart_name, serialize($products_array_idqty), time() + (3600 * 48)));
        $response->send();   
    }

    public function checkCartAjaxAction()
    {
        $products_array_idqty = $this->getProductsArrayFromCookies(0,0);
        
        $cartIsEmpty = false;
        if (count($products_array_idqty) === 0) {
            $cartIsEmpty = true;
        } else {
            $products_ids = array_keys($products_array_idqty);
            if ((count($products_ids) === 1) and ($products_ids[0] === 0)) 
            {
                $cartIsEmpty = true;
            }
        }
        return $this->json(array('cartIsEmpty'=> $cartIsEmpty, 'products_ids'=>$products_array_idqty));
    }

    public function orderAction()
    {
        $request = new Request(
        $_GET,
        $_POST,
        array(),
        $_COOKIE,
        $_FILES,
        $_SERVER
        );

        $cookies = $request->cookies;
        if ($cookies->has('koshik_sushi_cart'))
        {
            $products_array_idqty = unserialize($cookies->get('koshik_sushi_cart'));
        }
        else 
        {
            return $this->redirectToRoute('sushi_sushi_homepage');   
        }
        
        if (count($products_array_idqty) === 0) {
            return $this->redirectToRoute('sushi_sushi_homepage');
        } else {

        }
        $products_ids = array_keys($products_array_idqty);
        if ((count($products_ids) === 1) & ($products_ids[0] === 0)) 
        {
            return $this->redirectToRoute('sushi_sushi_homepage');
        } else {

        }
       return $this->render('SushiSushiBundle:Page:delivery.html.twig');
       //return $this->render('SushiSushiBundle:Page:chopsticks.html.twig');
    }
        
    public function deliveryAction()
    {
       //return $this->redirectToRoute('sushi_sushi_homepage');
       return $this->render('SushiSushiBundle:Page:delivery.html.twig');
    }
    
    public function orderfinishAction(Request $request)
    {
       /*$request = new Request(
        $_GET,
        $_POST,
        array(),
        $_COOKIE,
        $_FILES,
        $_SERVER
        );*/
        
        $cookies = $request->cookies;

        if ($cookies->has('koshik_sushi_cart'))
        {
            $products_array_idqty = unserialize($cookies->get('koshik_sushi_cart'));
        }
        else 
        {
            return $this->redirectToRoute('sushi_sushi_homepage');
            $products_array_idqty[0] = 0;//"empy cart";           
        }
        
        $products_ids = array_keys($products_array_idqty);

        //$products_ids = array_keys($products_array_idqty);
        
        if ((count($products_ids) === 1) & ($products_ids[0] === 0)) {
            return $this->redirectToRoute('sushi_sushi_homepage');
        }        
        
        $em = $this->getDoctrine()
                   ->getManager();
        $em_qb = $em->createQueryBuilder();
        

        $products_cart_q = $em_qb
                    ->select('pr_cart')
                    ->from('SushiSushiBundle:Products',  'pr_cart')
                    ->where('pr_cart.id IN (:products_ids)')
                    ->addOrderBy('pr_cart.orderProduct', 'ASC')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getResult();
        
        $sumOrderTotal = 0;
        foreach ($products_cart_q as $curProduct) {
            $sumOrderTotal = $sumOrderTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
        }
        $currDate = new \DateTime();
        $newOrder = new Orders();
        $newOrder->setDate($currDate);
        $newOrder->setCustomerId(0);
        $newOrder->setCustomerName($request->request->get('customer_name'));
        $newOrder->setCustomerPhone($request->request->get('customer_phone'));
        $newOrder->setSticksQty($request->request->get('_addstick'));
        $newOrder->setStickStudQty($request->request->get('_addstick_stud'));
        $newOrder->setDelZoneId(0);
        $newOrder->setDelPrice(0);
        $newOrder->setStreetId(0);
        $newOrder->setStreetName($request->request->get('address_street'));
        $newOrder->setBuilding($request->request->get('address_building'));
        $newOrder->setPorch($request->request->get('address_porch'));
        $newOrder->setApp($request->request->get('address_apps'));
        $newOrder->setSum($sumOrderTotal);
        //
        $isInTimeOrder = $request->request->get('isInTimeOrder');
        if ($isInTimeOrder == 'on') {
            $isInTimeOrder = true;
        } else {
            $isInTimeOrder = false;
        }
        $deliveryDate = $request->request->get('address_date');
        $deliveryDateHours = $request->request->get('address_hours');
        $deliveryDateMinutes = $request->request->get('address_minutes');
       try {
            $deliveryDateTime = \DateTime::createFromFormat('Y-m-d', $deliveryDate);
            $deliveryDateTime->setTime($deliveryDateHours, $deliveryDateMinutes, 0);
            }
        catch(Exception $e) {
            $deliveryDateTime = new \DateTime();
        }
        $newOrder->setDelInTime($isInTimeOrder);
        $newOrder->setDelTime($deliveryDateTime);
        //
        $em->persist($newOrder);
        $em->flush();
        $newOrderId = $newOrder->getId();
        $it = 0;
        foreach ($products_cart_q as $curProduct) {
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $products_array_idqty[$curProduct->getId()]);
            $newOrderProducts = new OrdersProducts();
            $newOrderProducts->setOrderId($newOrderId);
            $newOrderProducts->setRowId($curProduct->getid());//$it);
            $newOrderProducts->setProductId($curProduct->getid());
            $newOrderProducts->setQty($products_array_idqty[$curProduct->getId()]);
            $newOrderProducts->setPrice($curProduct->getprice());
            $newOrderProducts->setSum($products_array_idqty[$curProduct->getId()]*$curProduct->getprice());
            $em->persist($newOrderProducts);
            $em->flush();
            $it++;
        }

        unset($products_array_idqty);
       
        $response = new Response();
        $products_array_idqty[0] = 0;
        $response->headers->setCookie(new Cookie('koshik_sushi_cart', serialize($products_array_idqty), time() + (3600 * 48)));
        $response->send();
        //$fieldvalue = $request->request->get('_addstick');
        //$fieldvalue = $request->query->get('_addstick');
        
        //$newOrderId = new DateTime( $deliveryDate . ' '. $deliveryDateHours . ':' . $deliveryDateMinutes);
        //$newOrderId = new DateTime( $deliveryDate . ' 00:00');
        
        //$newOrderId =  $deliveryDate . ' '. $deliveryDateHours . ':' . $deliveryDateMinutes;
        return $this->render('SushiSushiBundle:Page:orderfinish.html.twig', array('order_id' => $newOrderId));
        //return $this->render('SushiSushiBundle:Page:orderfinish.html.twig', array('order_id' => $fieldvalue));
    }

}