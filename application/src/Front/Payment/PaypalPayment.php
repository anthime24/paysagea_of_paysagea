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
use Swift_Mailer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaypalPayment extends AbstractPayment
{
    const PAYPAL_VERSION = 109.0;
    const PAYPAL_SITE_BASE = 'https://www.paypal.com';
    const PAYPAL_SITE_EXPRESS_CHECKOUT = 'https://api-3t.paypal.com';
    const PAYPAL_SANDBOX_SITE_BASE = 'https://www.sandbox.paypal.com';
    const PAYPAL_SANDBOX_SITE_EXPRESS_CHECKOUT = 'https://api-3t.sandbox.paypal.com';
    const PAYPAL_PAYMENTREQUEST_0_PAYMENTACTION = 'SALE';
    const PAYPAL_PAYMENTREQUEST_0_CURRENCYCODE = 'EUR';
    const PAYPAL_METHOD_SET = 'SetExpressCheckout';
    const PAYPAL_METHOD_DO = 'DoExpressCheckoutPayment';
    public const PAYPAL_METHOD_GET = 'GetExpressCheckoutDetails';

    /**
     * @var string
     */
    private $paypalUser;

    /**
     * @var string
     */
    private $paypalPassword;

    /**
     * @var string
     */
    private $paypalSignature;

    /**
     * @var string
     */
    private $paypalSandboxUser;

    /**
     * @var string
     */
    private $paypalSandboxPassword;

    /**
     * @var string
     */
    private $paypalSandboxSignature;

    public function __construct(
        string $projectDir,
        EntityManagerInterface $entityManager,
        Swift_Mailer $mailer,
        Session $session,
        RequestStack $requestStack,
        InscriptionService $inscriptionService,
        array $paymentProxiesTest,
        string $paypalUser,
        string $paypalPassword,
        string $paypalSignature,
        string $paypalSandboxUser,
        string $paypalSandboxPassword,
        string $paypalSandboxSignature
    ) {
        $this->projectDir = $projectDir;
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->request = $this->requestStack->getCurrentRequest();
        $this->inscriptionService = $inscriptionService;
        $this->paymentProxiesTest = $paymentProxiesTest;
        $this->paypalUser = $paypalUser;
        $this->paypalPassword = $paypalPassword;
        $this->paypalSignature = $paypalSignature;
        $this->paypalSandboxUser = $paypalSandboxUser;
        $this->paypalSandboxPassword = $paypalSandboxPassword;
        $this->paypalSandboxSignature = $paypalSandboxSignature;
    }

    public function isPaypalPayment()
    {
        return $this->request->get('token') && $this->request->get('PayerID');
    }

    public function prepare(
        ?Client $client,
        ?Creation $creation,
        Offre $offer,
        bool $privateCreation,
        ?CodePromo $promoCode,
        float $amount,
        string $successUrl,
        string $cancelUrl,
        bool $withoutAccount = false
    ) {
        $customInformationsFile = $this->saveCustomInformations(
            'paypal',
            $client,
            $creation,
            $offer,
            $privateCreation,
            $promoCode,
            $amount,
            null
        );

        $url = $this->getPaypalSiteExpressCheckout() . '/nvp?';
        $url .= http_build_query(
            array(
                'VERSION' => self::PAYPAL_VERSION,
                'USER' => $this->getPaypalUser(),
                'PWD' => $this->getPaypalPassword(),
                'SIGNATURE' => $this->getPaypalSignature(),
                'METHOD' => self::PAYPAL_METHOD_SET,
                'LANDINGPAGE' => 'Login',
                'SOLUTIONTYPE' => 'Mark',
                'CANCELURL' => $cancelUrl,
                'RETURNURL' => $successUrl,
                'L_PAYMENTREQUEST_0_AMT0' => $amount,
                'L_PAYMENTREQUEST_0_ITEMAMT' => $amount,
                'L_PAYMENTREQUEST_0_NAME0' => $offer != null ? $offer->getNom() : '',
                'PAYMENTREQUEST_0_AMT' => $amount,
                'PAYMENTREQUEST_0_CUSTOM' => $customInformationsFile,
                'PAYMENTREQUEST_0_CURRENCYCODE' => self::PAYPAL_PAYMENTREQUEST_0_CURRENCYCODE,
                'PAYMENTREQUEST_0_PAYMENTACTION' => self::PAYPAL_PAYMENTREQUEST_0_PAYMENTACTION
            )
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $errorMessage = null;
        $paypalParams = array();

        if (empty($result)) {
            $errorMessage = curl_error($ch);
        } else {
            parse_str($result, $paypalParams);
        }

        curl_close($ch);

        if ($errorMessage == null && !array_key_exists('TOKEN', $paypalParams)) {
            $errorMessage = $paypalParams['L_LONGMESSAGE0'];
        }

        if (!empty($errorMessage)) {
            throw new HttpException(500, 'Erreur de connexion avec Paypal : ' . $errorMessage);
        }

        /*
        if ($withoutAccount) {
            $returnUrl = $this->getPaypalSiteBase(
                ) . '/webapps/xoonboarding?country.x=FR&exp=guest&flow=1-P&locale.x=fr_FR&token=' . $paypalParams['TOKEN'];
        } else {
            $returnUrl = $this->getPaypalSiteBase() . '/webscr&cmd=_express-checkout&token=' . $paypalParams['TOKEN'];
        }
        */

        $returnUrl = $this->getPaypalSiteBase() . '/webscr&cmd=_express-checkout&token=' . $paypalParams['TOKEN'];
        return $returnUrl;
    }

    public function done(): ?ClientPaiement
    {
        $clientPayment = null;

        if ($this->isPaypalPayment()) {
            $token = $this->request->get('token');
            $payerId = $this->request->get('PayerID');
            $paypalParamsDetails = $this->getDetails($token);

            if (!isset($paypalParamsDetails['ACK']) || $paypalParamsDetails['ACK'] != 'Success') {
                throw new HttpException(500, 'Erreur de connexion avec Paypal.');
            }

            $reference = $paypalParamsDetails['PAYMENTREQUEST_0_CUSTOM'];
            $customInformations = $this->getCustomInformations('paypal', $reference);

            $url = $this->getPaypalSiteExpressCheckout() . '/nvp?';
            $url .= http_build_query(
                array(
                    'VERSION' => self::PAYPAL_VERSION,
                    'USER' => $this->getPaypalUser(),
                    'PWD' => $this->getPaypalPassword(),
                    'SIGNATURE' => $this->getPaypalSignature(),
                    'METHOD' => self::PAYPAL_METHOD_DO,
                    'PAYMENTREQUEST_0_AMT' => $customInformations['amount'],
                    'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
                    'PAYMENTREQUEST_0_PAYMENTACTION' => self::PAYPAL_PAYMENTREQUEST_0_PAYMENTACTION,
                    'PayerID' => $payerId,
                    'TOKEN' => $token
                )
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            $errorMessage = null;
            $paypalParams = array();

            if (empty($result)) {
                $errorMessage = curl_error($ch);
            } else {
                parse_str($result, $paypalParams);
            }

            curl_close($ch);

            if (!empty($errorMessage)) {
                throw new HttpException(500, 'Erreur de connexion avec Paypal : ' . $errorMessage);
            }

            // On enregistre la transaction
            $clientPayment = new ClientPaiement();
            $clientPayment->setNumTransaction($paypalParams['PAYMENTINFO_0_TRANSACTIONID']);
            $clientPayment->setDatePaiement(new DateTime());
            $clientPayment->setClient($customInformations['client']);
            $clientPayment->setReference($reference);
            $clientPayment->setMontantPaiement($customInformations['amount']);
            $errorMessage = null;

            if ($paypalParams['ACK'] == 'Success') {
                $clientPayment->setValide(true);
                $clientPayment->setReponseCode('0');
                $clientPayment->setReponseCodeTexte('Success');
            } else {
                if ($paypalParams['ACK'] == 'SuccessWithWarning') {
                    $clientPayment->setValide(true);
                } else {
                    $clientPayment->setValide(false);
                }
                $clientPayment->setReponseCode($paypalParams['L_ERRORCODE0']);
                $clientPayment->setReponseCodeTexte(
                    $paypalParams['L_SHORTMESSAGE0'] . ' - ' . $paypalParams['L_LONGMESSAGE0']
                );
                $errorMessage = $paypalParams['L_SHORTMESSAGE0'] . ' - ' . $paypalParams['L_LONGMESSAGE0'];
            }

            $this->em->persist($clientPayment);
            $this->em->flush();

            if (!empty($errorMessage)) {
                throw new HttpException(500, 'Erreur avec Paypal : ' . $errorMessage);
            }

            if ($clientPayment->getValide()) {
                $this->inscriptionService->validate(
                    $customInformations['client'],
                    $customInformations['creation'],
                    $customInformations['offer'],
                    $customInformations['privateCreation'],
                    $customInformations['promoCode']
                );
            }
        }

        return $clientPayment;
    }

    private function getDetails(string $token)
    {
        $url = $this->getPaypalSiteExpressCheckout() . '/nvp?';
        $url .= http_build_query(
            array(
                'VERSION' => self::PAYPAL_VERSION,
                'USER' => $this->getPaypalUser(),
                'PWD' => $this->getPaypalPassword(),
                'SIGNATURE' => $this->getPaypalSignature(),
                'METHOD' => self::PAYPAL_METHOD_GET,
                'TOKEN' => $token
            )
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $errorMessage = null;
        $paypalParams = array();

        if (empty($result)) {
            $errorMessage = curl_error($ch);
        } else {
            parse_str($result, $paypalParams);
        }

        curl_close($ch);

        if (!empty($errorMessage)) {
            throw new HttpException(500, 'Erreur de connexion avec Paypal : ' . $errorMessage);
        }

        return $paypalParams;
    }

    private function getPaypalUser(): ?string
    {
        return $this->isTestMode() ? $this->paypalSandboxUser : $this->paypalUser;
    }

    private function getPaypalPassword(): ?string
    {
        return $this->isTestMode() ? $this->paypalSandboxPassword : $this->paypalPassword;
    }

    private function getPaypalSignature(): ?string
    {
        return $this->isTestMode() ? $this->paypalSandboxSignature : $this->paypalSignature;
    }

    private function getPaypalSiteBase(): ?string
    {
        return $this->isTestMode() ? self::PAYPAL_SANDBOX_SITE_BASE : self::PAYPAL_SITE_BASE;
    }

    private function getPaypalSiteExpressCheckout(): ?string
    {
        return $this->isTestMode() ? self::PAYPAL_SANDBOX_SITE_EXPRESS_CHECKOUT : self::PAYPAL_SITE_EXPRESS_CHECKOUT;
    }
}
