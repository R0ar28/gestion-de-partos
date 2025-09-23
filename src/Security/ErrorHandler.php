<?php
declare(strict_types=1);

namespace App\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ErrorHandler
{
    public function __construct(
        private Environment $twig
    ) {}

    /**
     * Maneja errores 404 (Not Found).
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleNotFound(): Response
    {
        $content = $this->twig->render(
            'bundles/TwigBundle/Exception/error404.html.twig'
        );

        return new Response($content, Response::HTTP_NOT_FOUND);
    }

    /**
     * Maneja cualquier otra excepción.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleError(\Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $statusText = Response::$statusTexts[$statusCode] ?? 'Unknown error';

        $content = $this->twig->render(
            'bundles/TwigBundle/Exception/error.html.twig',
            [
                'status_code' => $statusCode,
                'status_text' => $statusText,
                'exception'   => $exception,
            ]
        );

        return new Response($content, $statusCode);
    }
}