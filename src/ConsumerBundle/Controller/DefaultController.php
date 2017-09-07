<?php

namespace ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/service-b")
     */
    public function indexAction()
    {
        return $this->render('ConsumerBundle:Default:index.html.twig');
    }
}
