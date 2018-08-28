<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{

    public function logout()
    {
        //empty body
    }

    public function authenticate()
    {
        //empty body
    }

    public function login()
    {
        return $this->render('login.html.twig');
    }
}