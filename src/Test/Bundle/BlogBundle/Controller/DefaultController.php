<?php

namespace Test\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $name = $this->get('blog.name_wrapper')->wrap($name);

        return $this->render('BlogBundle:Default:index.html.twig', array('name' => $name));
    }
}