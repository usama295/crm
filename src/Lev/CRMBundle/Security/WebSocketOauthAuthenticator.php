<?php
namespace App\Lev\CRMBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;

class WebSocketOauthAuthenticator implements SimpleFormAuthenticatorInterface, AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var LoggerInterface|NullLogger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, Logger $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger   = $logger;
        $this->logger->info('SOCKET: WebSocketOauthAuthenticator __CONSTRUCT ');
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $this->getUserByAccessTokenAndUsername($token->getCredentials(), $token->getUsername());

        return new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    protected function getUserByAccessTokenAndUsername($accessToken, $username)
    {
        $token = $this->doctrine->getManager()->getRepository('LevCRMBundle:Oauth2\AccessToken')
            ->createQueryBuilder('t')
            ->innerJoin('t.user', 'u')
            ->where('t.token = ?1')
            ->setParameters(array(1 => $accessToken))
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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse("Authentication Failed.", 403);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new JsonResponse("Authentication Ok.", 200);
    }

}
