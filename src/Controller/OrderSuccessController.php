<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/fin/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, OrderRepository $orderRepository, Cart $cart): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()){
            $order->setIsPaid(1);
            $this->entityManager->flush();
            $cart->remove();
            // send email commande
        }


        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
