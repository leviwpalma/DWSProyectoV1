<?php

namespace Controllers;

class HomeController
{
    public function index(): void
    {
        require __DIR__ . '/../views/home/index.php';
    }
}