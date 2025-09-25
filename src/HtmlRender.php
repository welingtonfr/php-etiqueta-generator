<?php

namespace Welin\PhpEtiquetaGenerator;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HtmlRender
{
    private Environment $environment;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('src/Templates');
        $this->environment = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(string $template, array $context)
    {
       return $this->environment->render($template, $context);
    }
}