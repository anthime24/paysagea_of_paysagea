<?php

namespace App\Front\Payment;

use App\Core\Entity\Client;
use App\Core\Entity\CodePromo;
use App\Core\Entity\Creation;
use App\Core\Entity\Offre;
use App\Front\Service\InscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractPayment
{
    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var InscriptionService
     */
    protected $inscriptionService;

    /**
     * @var array
     */
    protected $paymentProxiesTest;

    public function saveCustomInformations(
        string $paymentMethod,
        ?Client $client = null,
        ?Creation $creation = null,
        ?Offre $offer = null,
        bool $privateCreation = false,
        ?CodePromo $promoCode = null,
        float $amount = null,
        string $redirection = null
    ): ?string {
        $array = array(
            'clientId' => $client ? $client->getId() : null,
            'creationId' => $creation ? $creation->getId() : null,
            'offerId' => $offer ? $offer->getId() : null,
            'privateCreation' => $privateCreation,
            'amount' => $amount,
            'promoCodeId' => $promoCode ? $promoCode->getId() : null,
            'redirection' => $redirection,
            'timestamp' => time()
        );

        $file = null;

        if ($this->projectDir && !empty($paymentMethod)) {
            $path = $this->projectDir . '/var/' . $paymentMethod . '/' . date('Y/m/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $file = time() . '.txt';

            file_put_contents($path . $file, json_encode($array));
        }

        return $file;
    }

    protected function getCustomInformations(string $paymentMethod, string $file): ?array
    {
        $customInformations = null;

        if ($this->projectDir && !empty($paymentMethod)) {
            $filePath = $this->projectDir . '/var/' . $paymentMethod . '/' . date('Y/m/') . $file;

            if (file_exists($filePath)) {
                $customInformationsArray = json_decode(file_get_contents($filePath), true);
                $customInformations = array(
                    'client' => $customInformationsArray['clientId'] ? $this->em->getRepository(Client::class)->find(
                        $customInformationsArray['clientId']
                    ) : null,
                    'creation' => $customInformationsArray['creationId'] ? $this->em->getRepository(
                        Creation::class
                    )->find($customInformationsArray['creationId']) : null,
                    'offer' => $customInformationsArray['offerId'] ? $this->em->getRepository(Offre::class)->find(
                        $customInformationsArray['offerId']
                    ) : null,
                    'privateCreation' => $customInformationsArray['privateCreation'],
                    'amount' => $customInformationsArray['amount'],
                    'promoCode' => $customInformationsArray['promoCodeId'] ? $this->em->getRepository(
                        CodePromo::class
                    )->find($customInformationsArray['promoCodeId']) : null,
                    'redirection' => $customInformationsArray['redirection'],
                    'timestamp' => time()
                );
            }
        }

        return $customInformations;
    }

    protected function isTestMode(): bool
    {
        return false; //$this->request && $this->paymentProxiesTest && in_array($this->request->getClientIp(), $this->paymentProxiesTest);
    }
}
