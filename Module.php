<?php
/**
 * Created by PhpStorm.
 * User: Harry
 * Date: 15-5-2018
 * Time: 16:06
 */
namespace NIOLAB\oauth2;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use NIOLAB\oauth2\components\repositories\AuthCodeRepository;
use NIOLAB\oauth2\components\repositories\RefreshTokenRepository;
use NIOLAB\oauth2\components\web\ServerRequest;
use NIOLAB\oauth2\components\web\ServerResponse;
use NIOLAB\oauth2\components\repositories\AccessTokenRepository;
use NIOLAB\oauth2\components\repositories\ClientRepository;
use NIOLAB\oauth2\components\repositories\ScopeRepository;
use NIOLAB\oauth2\controllers\AuthorizeController;
use NIOLAB\oauth2\controllers\ClientsController;
use NIOLAB\oauth2\controllers\TokenController;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\UrlRule;
use yii\web\GroupUrlRule;

class Module extends \yii\base\Module implements BootstrapInterface {

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'NIOLAB\oauth2\controllers';


    /**
     * @var string Class to use as UserRepository
     */
    public $userRepository;

    /**
     * @var string Alias to the private key file
     */
    public $privateKey;

    /**
     * @var string Alias to the public key file
     */
    public $publicKey;

    /**
     * @var string A random encryption key. For example you could create one with base64_encode(random_bytes(32))
     */
    public $encryptionKey;

    /**
     * @var bool Enable the Client Credentials Grant (https://oauth2.thephpleague.com/authorization-server/client-credentials-grant/)
     */
    public $enableClientCredentialsGrant = true;

    /**
     * @var bool Enable the Password Grant (https://oauth2.thephpleague.com/authorization-server/resource-owner-password-credentials-grant/)
     */
    public $enablePasswordGrant = true;

    /**
     * @var bool Enable the Authorization Code Grant (https://oauth2.thephpleague.com/authorization-server/auth-code-grant/)
     */
    public $enableAuthorizationCodeGrant = true;

    /**
     * @var bool Enable the Implicit Grant (https://oauth2.thephpleague.com/authorization-server/implicit-grant/
     */
    public $enableImplicitGrant = false;

    public $urlManagerRules = [];

    public $enableClientsController = true;

    public $controllerMap = [
        'authorize' => [
            'class' => AuthorizeController::class,
            'as corsFilter' => Cors::class,
        ],
        'token' => [
            'class' => TokenController::class,
            'as corsFilter' => Cors::class,
        ],
        'clients' => [
            'class' => ClientsController::class,
        ]
    ];


    /**
     * Sets module's URL manager rules on application's bootstrap.
     * @param Application $app
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()
            ->addRules([
                [
                    'class' => UrlRule::class,
                    'pluralize' => false,
                    'only' => ['create', 'options'],
                    'extraPatterns' => [
                        'OPTIONS <action:\w+>' => 'options'
                    ],
                    'controller' => [$this->uniqueId . '/token']
                ]
            ],false);
    }

    /**
     * @return null|AuthorizationServer
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuthorizationServer() {
        if (!$this->has('server')) {

            $clientRespository = new ClientRepository();
            $accessTokenRepository = new AccessTokenRepository();
            $authCodeRepository = new AuthCodeRepository();
            $refreshTokenRepository = new RefreshTokenRepository();
            $userRepository = new $this->userRepository;
            $scopeRepository = new ScopeRepository();

            $server = new AuthorizationServer(
                $clientRespository,
                $accessTokenRepository,
                $scopeRepository,
                \Yii::getAlias($this->privateKey),
                $this->encryptionKey
            );

            $enableRefreshGrant = false;

            /* Client Credentials Grant */
            if ($this->enableClientCredentialsGrant) {
                $server->enableGrantType(
                    new ClientCredentialsGrant(),
                    new \DateInterval('PT1H')
                );
            }

            /* Client Credentials Grant */
            if ($this->enableImplicitGrant) {
                $server->enableGrantType(
                    new ImplicitGrant(new \DateInterval('PT1H')),
                    new \DateInterval('PT1H')
                );
            }

            /* Password Grant */
            if ($this->enablePasswordGrant) {
                $server->enableGrantType(new PasswordGrant(
                    $userRepository,
                    $refreshTokenRepository
                ));
                $enableRefreshGrant = true;
            }

            /* Authorization Code Flow Grant */
            if ($this->enableAuthorizationCodeGrant) {
                $grant = new AuthCodeGrant(
                    $authCodeRepository,
                    $refreshTokenRepository,
                    new \DateInterval('P1M')
                );
                $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
                $server->enableGrantType($grant);
                $enableRefreshGrant = true;
            }

            /* Refresh Token Grant */
            if ($enableRefreshGrant) {
                $grant = new RefreshTokenGrant(
                    $refreshTokenRepository
                );
                $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
                $server->enableGrantType($grant);
            }

            $this->set('server',$server);
        }
        return $this->get('server');
    }


    /**
     * @var ServerRequest
     */
    private $_psrRequest;

    /**
     * Create a PSR-7 compatible request from the Yii2 request object
     * @return ServerRequest|static
     */
    public function getRequest() {
        if ($this->_psrRequest === null) {
            $request = \Yii::$app->request;
            $this->_psrRequest = (new ServerRequest($request))->withParsedBody($request->bodyParams)->withQueryParams($request->queryParams);
        }
        return $this->_psrRequest;
    }


    /**
     * @var ServerResponse
     */
    private $_psrResponse;

    /**
     * @return ServerResponse|static
     */
    public function getResponse() {
        if ($this->_psrResponse === null) {
            $this->_psrResponse = new ServerResponse();
        }
        return $this->_psrResponse;
    }

}