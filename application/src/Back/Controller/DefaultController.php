<?php

namespace App\Back\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('back/default/index.html.twig', array('name' => $name));
    }
}
