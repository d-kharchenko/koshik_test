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
use Sushi\SushiBundle\Entity\Warehouses;
use Sushi\SushiBundle\Entity\DelZones;
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
            /*$products = $this->getDoctrine()->getManager()//getEntityManager()
                ->getRepository('SushiSushiBundle:Products')
                ->findAllProductsByGroupId($curGroupId);*/
            $products = $this->getDoctrine()->getManager()
                ->getRepository('SushiSushiBundle:Products')
                ->findBy(array('groupId' => $curGroupId, 'status' => 1), array('orderProduct' => 'ASC'));
            $groupsProducts[$it] = array('groupId' => $curGroupId, 'groupName' => $curGroup->getName(), 'products' => $products);
            $it++;
        }
        
        $cartArray = $this->getCartArrayFromCoockies();
        $sumOrderTotal = $cartArray['sumOrderTotal'];
        $itemCountTotal = $cartArray['itemCountTotal'];
        $sortedCart = $cartArray['sortedCart'];
        $sumFreeDeliveryPrice = $cartArray['sumFreeDeliveryPrice'];
        $sumBeforeFreeDeliveryPrice = $cartArray['sumBeforeFreeDeliveryPrice'];
       
        return $this->render('SushiSushiBundle:Page:catalog.html.twig', array('groups_products' => $groupsProducts, 'products' => $products, 'products_cart' => $sortedCart, 'sumOrderTotal' => number_format($sumOrderTotal, 2, '.', ''), 'itemCountTotal' => $itemCountTotal, 'isAjax' => true, 'sumFreeDeliveryPrice' => $sumFreeDeliveryPrice));
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
      

    public function setCartItemCountAjaxAction($product_id, $qty, $isSet) 
    {
        $products_array_idqty = $this->getProductsArrayFromCookies($product_id, $qty, $isSet);
        $this->saveProductsArrayToCookies($products_array_idqty);
        
        $cartArray = $this->getCartArrayFromCoockies($products_array_idqty);
        $sumOrderTotal = $cartArray['sumOrderTotal'];
        $itemCountTotal = $cartArray['itemCountTotal'];
        $sortedCart = $cartArray['sortedCart'];
        $sumFreeDeliveryPrice = $cartArray['sumFreeDeliveryPrice'];
        $sumBeforeFreeDeliveryPrice = $cartArray['sumBeforeFreeDeliveryPrice'];
        
        $response_json = $this->json(array('products_array_idqty' => $products_array_idqty, 'products_cart' => $sortedCart, 'sumOrderTotal' => $sumOrderTotal,'itemCountTotal' => $itemCountTotal, 'isAjax' => true, 'sumFreeDeliveryPrice' => $sumFreeDeliveryPrice, 'sumBeforeFreeDeliveryPrice' => $sumBeforeFreeDeliveryPrice));
        return $response_json;
                
    }

    public function SortArrayByCookie($products_array_idqty, $products_cart)
    {
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
                        $products_array_idqty[$product_id] = (int) $curQty;
                    } else {
                        $products_array_idqty[$product_id] = ((int) $products_array_idqty[$product_id]) + ((int) $curQty);
                    }
                }
                else
                {
                    $products_array_idqty[$product_id] = (int) $curQty;
                }
            }
            
        }
        else 
        {
            if ($product_id > 0)
            {
                $products_array_idqty[$product_id] = (int) $curQty;
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

        $em = $this->getDoctrine()
        ->getManager()
        ->createQueryBuilder();

        $delTypes = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:DelZones')
        ->findBy(array('status' => 1), array('sortOrder' => 'ASC'));

        $warehouses = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:Warehouses')
        ->findBy(array('status' => 1), array('sortOrder' => 'ASC'));
       
       return $this->render('SushiSushiBundle:Page:delivery.html.twig', array('delTypes' => $delTypes, 'warehouses' => $warehouses));
       //return $this->render('SushiSushiBundle:Page:chopsticks.html.twig');
    }
        
    public function deliveryAction()
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

        $em = $this->getDoctrine()
        ->getManager()
        ->createQueryBuilder();

        $delTypes_res = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:DelZones')
        ->findBy(array('status' => 1), array('sortOrder' => 'ASC'));
        
        $warehouses = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:Warehouses')
        ->findBy(array('status' => 1), array('sortOrder' => 'ASC'));
        
        $cartArray = $this->getCartArrayFromCoockiesWithGroups();
        $sumOrderTotal = $cartArray['sumOrderTotal'];
        $itemCountTotal = $cartArray['itemCountTotal'];
        $sortedCart = $cartArray['sortedCart'];
        $sumFreeDeliveryPrice = $cartArray['sumFreeDeliveryPrice'];
        $sumBeforeFreeDeliveryPrice = $cartArray['sumBeforeFreeDeliveryPrice'];

        if ($sumBeforeFreeDeliveryPrice > 0) {
            $delTypes = $delTypes_res;    
            $it = 0;
            foreach ($delTypes_res as $curDelType) {
                $delTypes[$it] = array("id" => $curDelType->getId(), "name" => $curDelType->getName(), "price" => $curDelType->getPrice(), "sortOrder" => $curDelType->getSortOrder(), "status" => $curDelType->getStatus(), "css" => $curDelType->getCSS());
                if ($it === 1){
                    $sumDeliveryPrice = $curDelType->getPrice();
                }
                $it++;
            }
        }else{
            $sumDeliveryPrice = 0;
            $it = 0;
            foreach ($delTypes_res as $curDelType) {
                $delTypes[$it] = array("id" => $curDelType->getId(), "name" => $curDelType->getName(), "price" => 0, "sortOrder" => $curDelType->getSortOrder(), "status" => $curDelType->getStatus(), "css" => $curDelType->getCSS());
                $it++;
            }
        }
        if ($sumBeforeFreeDeliveryPrice === 0){
            $sumDeliveryPrice = 0;
        }
        $sumOrderTotalWithDelivery = $sumOrderTotal +  $sumDeliveryPrice; 
       return $this->render('SushiSushiBundle:Page:delivery.html.twig', array('delTypes' => $delTypes, 'warehouses' => $warehouses, 'products_cart' => $sortedCart, 'sumOrderTotal' => number_format($sumOrderTotal, 2, '.', ''), 'sumFreeDeliveryPrice' => $sumFreeDeliveryPrice, 'sumDeliveryPrice' => number_format($sumDeliveryPrice, 2, '.', ''), 'sumOrderTotalWithDelivery' => number_format($sumOrderTotalWithDelivery, 2, '.', '')));
        //return $this->redirectToRoute('sushi_sushi_homepage');
       //return $this->render('SushiSushiBundle:Page:delivery.html.twig');
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
        $newOrder->setWarehouseId(0);
        $newOrder->setStreetName($request->request->get('address_street'));
        $newOrder->setBuilding($request->request->get('address_building'));
        $newOrder->setPorch($request->request->get('address_porch'));
        $newOrder->setApp($request->request->get('address_apps'));
        $newOrder->setComment($request->request->get('checkout-comment'));
        $newOrder->setSum($sumOrderTotal);
        //
        $curDelPrice = 0;
        $curDelType = $request->request->get('radio-sel');
        $CurrentFreeDeliveryPrice = $this->getCurrentFreeDeliveryPrice();
        if (is_numeric($curDelType)){
                $curDelTypeId = (int) $curDelType;
                $delTypes = $this->getDoctrine()->getManager()
                ->getRepository('SushiSushiBundle:DelZones')
                ->findBy(array('id' => $curDelTypeId));
                foreach ($delTypes as $delType) {
                    $curDelPrice = (int) $delType->getPrice();
                    break;
                }
        } else {
            $curDelTypeId = 0;
        }
        if ($sumOrderTotal >= $CurrentFreeDeliveryPrice){
            $curDelPrice = 0;
        }
        $newOrder->setDelZoneId($curDelTypeId);
        $newOrder->setDelPrice($curDelPrice);
        //
        if ($curDelPrice === 0) {
            $curWarehouse = $request->request->get('check-del');
            if (is_numeric($curWarehouse)){
                $curWarehouseId = (int) $curWarehouse;
            } else {
                $curWarehouseId = 0;
            }
            $newOrder->setWarehouseId($curWarehouseId);
        }
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
        echo $deliveryDate;
       try {
            $deliveryDateTime = \DateTime::createFromFormat('d.m.Y', $deliveryDate);
            $deliveryDateTime->setTime((int)$deliveryDateHours, (int)$deliveryDateMinutes, 0);
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

    public function getCurrentFreeDeliveryPrice(){
        
        $curPrice = 250;
        $GlParams = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:GlSettings')
        ->findBy(array('paramName' => 'OrderFreeShippingSum'));
        foreach ($GlParams as $GlParam) {
            $curPrice = (int) $GlParam->getParamValue();
            break;
        }
        return $curPrice;
    }

    public function getCartArrayFromCoockies($products_array_idqty = null){
        
        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();
        if ($products_array_idqty === null){
            $products_array_idqty = $this->getProductsArrayFromCookies(0,0);
        }

        
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
            $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1, 'sum'=>number_format($curProduct->getprice()*$CurQty, 2, '.', ''));
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
        $sumFreeDeliveryPrice = $this->getCurrentFreeDeliveryPrice();
        $sumBeforeFreeDeliveryPrice = max($sumFreeDeliveryPrice - $sumOrderTotal, 0);
        $sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);
        $cartArray = array('sortedCart' => $sortedCart, 'sumOrderTotal' => $sumOrderTotal, 'itemCountTotal' => $itemCountTotal, 'sumFreeDeliveryPrice' => $sumFreeDeliveryPrice, 'sumBeforeFreeDeliveryPrice' => $sumBeforeFreeDeliveryPrice);
        return $cartArray;

    }

    public function getCartArrayFromCoockiesWithGroups($products_array_idqty = null){
        
        $em = $this->getDoctrine()
                   ->getManager()
                   ->createQueryBuilder();
        if ($products_array_idqty === null){
            $products_array_idqty = $this->getProductsArrayFromCookies(0,0);
        }

        
        $products_ids = array_keys($products_array_idqty);

        $products_tmp = $em
                    ->select('pr_cartg.groupId')
                    ->from('SushiSushiBundle:Products',  'pr_cartg')
                    ->where('pr_cartg.id IN (:products_ids)')
                    ->distinct('pr_cartg.groupId')
                    ->getQuery()
                    ->setParameter('products_ids', $products_ids)
                    ->getScalarResult();
        $groups_ids = array_column($products_tmp, "groupId");

        $em = $this->getDoctrine()
        ->getManager()
        ->createQueryBuilder();
  
        $groups = $this->getDoctrine()->getManager()
            ->getRepository('SushiSushiBundle:Groups')
            ->findBy(array('id' => $groups_ids), array('orderGroup' => 'ASC'));

        $itg = 0;
        $sumOrderTotal = 0;
        $itemCountTotal = 0;
        foreach ($groups as $curGroup) {
            $curGroupId = $curGroup->getId();
            $sumGroupTotal = 0;
            $products_cart_q = $this->getProductsByIdsAndGroupId($products_ids, $curGroupId);
            $it = 0;
            $products_cart = array();
            foreach ($products_cart_q as $curProduct) {
                $CurQty = $products_array_idqty[$curProduct->getId()];
                $products_cart[$it] = array("id" => $curProduct->getid(), "name" => $curProduct->getName(), "description" => $curProduct->getdescription(), "price" => $curProduct->getprice(), "qty" => $CurQty, "row_num" => $it+1, 'sum'=>number_format($curProduct->getprice()*$CurQty, 2, '.', ''));
                $sumGroupTotal = $sumGroupTotal + $products_array_idqty[$curProduct->getId()]*$curProduct->getprice();
                $itemCountTotal = $itemCountTotal + $CurQty;
                $it++;
            }
            $sumOrderTotal = $sumOrderTotal + $sumGroupTotal;
            $groupsProducts[$itg] = array('groupId' => $curGroupId, 'groupName' => $curGroup->getName(), 'products' => $products_cart, 'sum' => number_format($sumGroupTotal, 2, '.', ''));
            $itg++;
        }
        //

        //return $this->json(array('ids'=>$products_array_idqty));
        //var_dump($products_cart);
        $sumFreeDeliveryPrice = $this->getCurrentFreeDeliveryPrice();
        $sumBeforeFreeDeliveryPrice = max($sumFreeDeliveryPrice - $sumOrderTotal, 0);
        //$sortedCart = $this->SortArrayByCookie($products_array_idqty, $products_cart);

        $cartArray = array('sortedCart' => $groupsProducts, 'sumOrderTotal' => $sumOrderTotal, 'itemCountTotal' => $itemCountTotal, 'sumFreeDeliveryPrice' => $sumFreeDeliveryPrice, 'sumBeforeFreeDeliveryPrice' => $sumBeforeFreeDeliveryPrice);
        return $cartArray;

    }
    
    function getProductsByIdsAndGroupId($products_ids, $groupId){
        
         $products = $this->getDoctrine()->getManager()
        ->getRepository('SushiSushiBundle:Products')
        ->findBy(array('id' => $products_ids, 'groupId' => $groupId), array('orderProduct' => 'ASC'));
        return $products;
    }

}