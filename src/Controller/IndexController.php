<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        throw $this->createNotFoundException();
    }

    public function logout()
    {
        throw $this->createAccessDeniedException();
    }
}