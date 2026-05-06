<?php

namespace App\Front\Controller;

use App\Core\Entity\Creation;
use App\Core\Entity\Pub;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(EntityManagerInterface $em): Response
    {
        $pub = $em->getRepository(Pub::class)->findOneBy(array('id' => 1, 'actif' => true));
        $posts = array();

        return $this->render(
            'front/home/index.html.twig',
            array(
                'pub' => $pub,
                'posts' => $posts
            )
        );
    }

    public function indexFacebook(
        EntityManagerInterface $em,
        int $creationId,
        string $hash,
        int $timestamp
    ): Response {
        $pub = $em->getRepository(Pub::class)->findOneBy(array('id' => 1, 'actif' => true));
        $posts = array();
        $creation = $em->getRepository(Creation::class)->findOneById($creationId);

        return $this->render(
            'front/home/index.html.twig',
            array(
                'pub' => $pub,
                'posts' => $posts,
                'creation' => $creation,
                'hash' => $hash,
                'timestamp' => $timestamp
            )
        );
    }

}
