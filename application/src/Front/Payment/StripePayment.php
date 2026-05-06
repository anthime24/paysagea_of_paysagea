<?php

namespace App\Front\Payment;

use App\Core\Entity\Client;
use App\Core\Entity\ClientPaiement;
use App\Core\Entity\CodePromo;
use App\Core\Entity\Creation;
use App\Core\Entity\Offre;
use App\Front\Service\InscriptionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Customer;
use Stripe\Stripe;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class StripePayment extends AbstractPayment
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $stripePublicKey;

    /**
     * @var string
     */
    private $stripeSecretKey;

    /**
     * @var string
     */
    private $stripeTestPublicKey;

    /**
     * @var string
     */
    private $stripeTestSecretKey;

    public function __construct(
        string $projectDir,
        EntityManagerInterface $entityManager,
        Swift_Mailer $mailer,
        Session $session,
        RequestStack $requestStack,
        InscriptionService $inscriptionService,
        Security $security,
        array $paymentProxiesTest,
        string $stripePublicKey,
        string $stripeSecretKey,
        string $stripeTestPublicKey,
        string $stripeTestSecretKey
    ) {
        $this->projectDir = $projectDir;
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->request = $this->requestStack->getCurrentRequest();
        $this->inscriptionService = $inscriptionService;
        $this->security = $security;
        $this->paymentProxiesTest = $paymentProxiesTest;
        $this->stripePublicKey = $stripePublicKey;
        $this->stripeSecretKey = $stripeSecretKey;
        $this->stripeTestPublicKey = $stripeTestPublicKey;
        $this->stripeTestSecretKey = $stripeTestSecretKey;
    }

    private function __setSecretApiKey() {
        $apiKey = $this->isTestMode() ? $this->stripeTestSecretKey : $this->stripeSecretKey;
        \Stripe\Stripe::setApiKey($apiKey);
    }

    public function generateFileInfoHash($fileInfoId){
        $fileInfoHash = hash('sha512', 'XXX_-56@uVW<Z' . $fileInfoId . '##42');
        return $fileInfoHash;
    }

    public function checkFileInfoHash($fileInfoId, $hashToCheck)
    {
        $correctHash = $this->generateFileInfoHash($fileInfoId);
        if($hashToCheck == $correctHash) {
            return true;
        } else {
            return false;
        }
    }
    
    public function createCheckout(?Client $client,
                                   ?Creation $creation,
                                   Offre $offer,
                                   bool $privateCreation,
                                   ?CodePromo $promoCode,
                                   float $amount,
                                    string $paymentInfoFile,
                                    $successUrl,
                                    $cancelUrl)
    {
        $paymentInfo = $this->getCustomInformations('stripe', $paymentInfoFile);

        $productName = $offer->getNom();
        if($privateCreation == true) {
            $productName = $productName . ' ( + Option créations privés)';
        }

        $this->__setSecretApiKey();
        $product = \Stripe\Product::create([
            'name' => $productName
        ]);

        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => $paymentInfo['amount'] * 100,
            'currency' => 'eur'
        ]);

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $price->id,
                'quantity' => 1
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl
        ]);
        return $checkoutSession;
    }

    public function done($sessionId, $fileInfoId): ClientPaiement
    {
        $this->__setSecretApiKey();

        $paymentInfo = $this->getCustomInformations('stripe', $fileInfoId);

        $clientPayment = new ClientPaiement();
        $clientPayment->setPaymentProcessor('stripe');
        $clientPayment->setNumTransaction($sessionId);
        $clientPayment->setDatePaiement(new \DateTime());
        $clientPayment->setClient($paymentInfo['client']);
        $clientPayment->setReference($sessionId);
        $clientPayment->setMontantPaiement($paymentInfo['amount']);

        $clientPayment->setValide(true);
        $clientPayment->setReponseCode('0');
        $clientPayment->setReponseCodeTexte('Success');

        $this->em->persist($clientPayment);
        $this->em->flush();

        $this->inscriptionService->validate(
            $paymentInfo['client'],
            $paymentInfo['creation'],
            $paymentInfo['offer'],
            $paymentInfo['privateCreation'],
            $paymentInfo['promoCode']
        );

        return $clientPayment;
    }

    public function getStripePublicKey(): ?string
    {
        return $this->isTestMode() ? $this->stripeTestPublicKey : $this->stripePublicKey;
    }

    private function getStripeSecretKey(): ?string
    {
        return $this->isTestMode() ? $this->stripeTestSecretKey : $this->stripeSecretKey;
    }

}
