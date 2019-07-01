<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);        

        $trans = $this->container->get('translator');

        $msg = $exception->getMessage();
        $cause = $exception->getPrevious();

        if ($cause) {
            if ($cause instanceof BadCredentialsException) {
                $msg = $trans->trans('validation.bad_credentials');
            }
        } else {
            if ($exception instanceof BadCredentialsException) {
                $msg = $trans->trans('validation.bad_credentials');
            }
        }

        return new JsonResponse([
            'message' => $msg,
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}