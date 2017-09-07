<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/service-a")
     */
    public function indexAction()
    {
        return $this->render('ProducerBundle:Default:index.html.twig');
    }
}
