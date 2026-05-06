<?php

namespace App\Front\Controller;

use App\Core\Entity\ClientPaiement;
use App\Core\Entity\CodePromo;
use App\Core\Entity\Creation;
use App\Core\Entity\Offre;
use App\Front\Form\PaymentType;
use App\Front\Payment\PaypalPayment;
use App\Front\Payment\StripePayment;
use App\Front\Service\InscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{

    private function __getSelectionDetail(EntityManagerInterface $em, Session $session, InscriptionService $inscriptionService)
    {
        if (!$session->has('mjmt_front_inscription_offer_id')) {
            throw $this->createNotFoundException('Offer id not found');
        }

        $offer = $em->getRepository(Offre::class)->find($session->get('mjmt_front_inscription_offer_id'));

        if (!$offer) {
            throw $this->createNotFoundException('Offer not found');
        }

        $creation = $session->get('mjmt_front_inscription_creation_id') ? $em->getRepository(Creation::class)->find(
            $session->get('mjmt_front_inscription_creation_id')
        ) : null;
        $privateCreation = $session->get('mjmt_front_inscription_private_creation', false);

        $promoCode = $session->get('mjmt_front_inscription_promo_code_id') != null ? $em->getRepository(
            CodePromo::class
        )->find($session->get('mjmt_front_inscription_promo_code_id')) : null;

        if($promoCode !== null) {
            $priceCalculation = $inscriptionService->priceCalculation($offer, $privateCreation, $promoCode->getCode(), false);
        } else {
            $priceCalculation = $inscriptionService->priceCalculation($offer, $privateCreation, null, false);
        }

        return array(
            'offer' => $offer,
            'creation' => $creation,
            'promoCode' => $promoCode,
            'privateCreation' => $privateCreation,
            'priceCalculation' => $priceCalculation
        );
    }

    public function selection(
        Request $request,
        EntityManagerInterface $em,
        Session $session,
        InscriptionService $inscriptionService,
        PaypalPayment $paypalPayment,
        StripePayment $stripePayment
    ): Response {

        $selectionDetail = $this->__getSelectionDetail($em, $session, $inscriptionService);
        $offer = $selectionDetail['offer'];
        $creation = $selectionDetail['creation'];
        $privateCreation = $selectionDetail['privateCreation'];
        $promoCode = $selectionDetail['promoCode'];
        $priceCalculation = $selectionDetail['priceCalculation'];

        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $paymentMethod = $data['paymentMethod'];

            if ($paymentMethod == 'paypal') {
                return $this->redirect(
                    $paypalPayment->prepare(
                        $this->getUser(),
                        $creation,
                        $offer,
                        $privateCreation,
                        $promoCode,
                        $priceCalculation['priceToPay'],
                        $this->generateUrl('mjmt_front_payment_response', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        $this->generateUrl('mjmt_front_payment_selection', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        true
                    )
                );
            } elseif ($paymentMethod == 'stripe') {
                return $this->render(
                    'front/payment/prepare_stripe.html.twig',
                    array(
                        'stripePublicKey' => $stripePayment->getStripePublicKey(),
                        'priceToPay' => $priceCalculation['priceToPay'],
                    )
                );
            }
        }

        return $this->render(
            'front/payment/selection.html.twig',
            array(
                'form' => $form->createView(),
                'offer' => $offer,
                'priceToPay' => $priceCalculation['priceToPay'],
                'publicPrice' => $priceCalculation['publicPrice'],
                'promotion' => $priceCalculation['promotion']
            )
        );
    }

    public function stripeCheckout(EntityManagerInterface $em,
                                   StripePayment $stripePayment,
                                   Session $session,
                                   InscriptionService $inscriptionService,
                                   Request $request)
    {

        $client = $this->getUser();

        $selectionDetail = $this->__getSelectionDetail($em, $session, $inscriptionService);
        $offer = $selectionDetail['offer'];
        $creation = $selectionDetail['creation'];
        $privateCreation = $selectionDetail['privateCreation'];
        $promoCode = $selectionDetail['promoCode'];
        $priceCalculation = $selectionDetail['priceCalculation'];

        $paymentInfoFile = $stripePayment->saveCustomInformations('stripe', $client, $creation, $offer, $privateCreation, $promoCode, $priceCalculation['priceToPay'], null);

        $successUrl = $this->generateUrl('mjmt_front_payment_stripeResponse', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $successUrl = $successUrl . "?session_id={CHECKOUT_SESSION_ID}";
        $successUrl = $successUrl . "&file_info_id=" . $paymentInfoFile;
        $successUrl = $successUrl . '&file_info_hash=' . $stripePayment->generateFileInfoHash($paymentInfoFile);

        $cancelUrl = $this->generateUrl('mjmt_front_payment_selection', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $checkoutSession = $stripePayment->createCheckout($client,
            $creation,
            $offer,
            $privateCreation,
            $promoCode,
            $priceCalculation['priceToPay'],
            $paymentInfoFile,
            $successUrl,
            $cancelUrl);

        return new JsonResponse(array('sessionId' => $checkoutSession->id));
    }

    public function stripeResponse(Request $request, EntityManagerInterface $em, Session $session, StripePayment $stripePayment)
    {
        $sessionId = $request->get('session_id');
        $fileInfoId = $request->get('file_info_id');
        $fileInfoHash = $request->get('file_info_hash');

        if($fileInfoHash === null) {
            throw new \Exception("Invalid stripe payment hash");
        }

        if($stripePayment->checkFileInfoHash($fileInfoId, $fileInfoHash)){
            $clientPayment = $stripePayment->done($sessionId, $fileInfoId);
            return $this->render(
                'front/payment/done.html.twig',
                array(
                    'valid' => $clientPayment->getValide(),
                    'amount' => $clientPayment->getMontantPaiement()
                )
            );
        } else {
            throw new \Exception("Invalid stripe payment hash");
        }
    }

    //PAYPAL RESPONSE
    public function response(TranslatorInterface $translator, Session $session, PaypalPayment $paypalPayment, StripePayment $stripePayment): Response
    {
        $clientPayment = null;

        try {
            if ($paypalPayment->isPaypalPayment()) {
                $clientPayment = $paypalPayment->done();
            }

            if ($clientPayment) {
                $session->clear();
                return $this->redirectToRoute(
                    'mjmt_front_payment_done',
                    array('clientPaymentId' => $clientPayment->getId())
                );
            } else {
                return new Response('Payment not found');
            }
        } catch(\Exception $ex) {
            dump($ex->getTrace());
        }
    }

    /**
     * @ParamConverter("clientPayment", options={"id" = "clientPaymentId"})
     */
    public function done(ClientPaiement $clientPayment): Response
    {
        if ($clientPayment->getClient() != $this->getUser()) {
            throw $this->createNotFoundException('Payment client not match');
        }

        return $this->render(
            'front/payment/done.html.twig',
            array(
                'valid' => $clientPayment->getValide(),
                'amount' => $clientPayment->getMontantPaiement(),
            )
        );
    }

}
