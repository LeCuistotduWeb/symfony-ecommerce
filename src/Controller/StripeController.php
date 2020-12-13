<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     * @param $reference
     * @param OrderRepository $orderRepository
     * @param ProductRepository $productRepository
     * @return Response
     * @throws ApiErrorException
     */
    public function index($reference, OrderRepository $orderRepository, ProductRepository $productRepository): Response
    {
        $stripeProducts = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $orderRepository->findOneByReference($reference);

        if(!$order){
            return new JsonResponse('error', 'order');
        }

        foreach ($order->getOrderDetails()->getValues() as $product){
            $productObject = $productRepository->findOneByName($product->getProduct());
            $stripeProducts[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN.'/uploads/'.$productObject->getThumbnail()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        // Carrier
        $stripeProducts[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51HxtVFB2WZBAOGI3frGbMH8Fgiht7lPWK4HsmSksVbCpVC15YXkLtjUgA5zamt0DrvgGjnG31nmUObFc9rJbEco500FBkjXj0C');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$stripeProducts],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN.'/commande/fin/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
