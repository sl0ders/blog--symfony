<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/post")]
class AdminPostController extends AbstractController
{
    #[Route("/", name: "admin_post_index")]
    public function index() {

    }
}
