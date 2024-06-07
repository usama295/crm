<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lev\CRMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{

    public function authAction(Request $request)
    {
        try {
            $user = $this->getUserByAccessTokenAndUsername(
                $request->get('username', 'none_username'),
                $request->get('access_token', 'none_access_token')
            );
            $token = new UsernamePasswordToken($user, null, 'socket', $user->getRoles());
            $this->get('security.context')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'msg' => $e->getMessage(),
                'username' => $request->get('username', 'none_username'),
                'access_token' => $request->get('access_token', 'none_access_token'),
                'code' => $e->getCode()
            ), 401);
        }

        return new JsonResponse(array(
            'msg' => 'You are successfully logged in!',
        ), 200);
    }

    protected function getUserByAccessTokenAndUsername($username, $accessToken)
    {
        $token = $this->get('doctrine')->getManager()->getRepository('LevCRMBundle:Oauth2\AccessToken')
            ->createQueryBuilder('t')
            ->innerJoin('t.user', 'u')
            ->where('t.token = ?1')
            ->andWhere('u.username = ?2')
            ->setParameters(array(
                1 => $accessToken,
                2 => $username
            ))
            ->getQuery()
            ->getOneOrNullResult();

          if (null === $token) {
              throw new AuthenticationException(
                  'The access token provided is invalid.',
                  401
              );
          }

          if ($token->getExpiresAt() < time()) {
              throw new AuthenticationException(
                  'The access token provided has expired.',
                  401
              );
          }

          if ($token->getUser()->getUsername() !== $username) {
              throw new AuthenticationException(
                  'Invalid username.',
                  401
              );
          }

          return $token->getUser();
    }

    public function loginAction(Request $request)
    {

        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        return new JsonResponse(array(
            'msg' => 'You need to login',
            'more' => $session->get('something', 'nothing')
        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('FOSUserBundle:Security:login.html.twig', $data);
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
