<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController {
    #[Route('/user/register')]
    public function register(): Response
    {
        return $this->render('pages/register.html.twig');
    }
}