<?php

namespace App\Classes;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    public $session;
    public $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add($id){
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }
        else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    public function get(){
        return $this->session->get('cart');
    }

    public function remove(){
        return $this->session->remove('cart');
    }

    public function delete($id){
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        return $this->session->set('cart', $cart);
    }

    public function decrease($id){
        $cart = $this->session->get('cart', []);
        if($cart[$id] > 1) {
            $cart[$id]--;
        }else {
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    public function getFull(){
        $cartComplete = [];

        if($this->get()){
            foreach ($this->get() as $id => $quantity){
                $productObject = $this->productRepository->findOneById($id);

                if(!$productObject){
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product'=> $productObject,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }
}