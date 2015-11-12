<?php

namespace demo\contactsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/contacts", name="contacts")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $fname = $request->get('firstname');
        $lname = $request->get('lastname');
        return array('fname' => $fname, 'lname' => $lname);
    }
}
