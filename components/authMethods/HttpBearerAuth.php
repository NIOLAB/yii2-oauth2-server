<?php
namespace NIOLAB\oauth2\components\authMethods;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use NIOLAB\oauth2\components\repositories\AccessTokenRepository;
use NIOLAB\oauth2\components\web\OauthHttpException;
use NIOLAB\oauth2\Module;
use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\HttpException;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

/**
 * Created by PhpStorm.
 * User: Harry_000
 * Date: 11-7-2018
 * Time: 10:50
 */

class HttpBearerAuth extends AuthMethod {

    private null|AccessTokenRepository $_accessTokenRepository = null;


    /**
     * Authenticates the current user.
     * @param User $user
     * @param Request $request
     * @param Response $response
     * @return IdentityInterface the authenticated user identity. If authentication information is not provided, null will be returned.
     * @throws HttpException
     * @throws OauthHttpException
     */
    public function authenticate($user, $request, $response): IdentityInterface
    {

        /** @var Module $module */
        $module = Yii::$app->getModule('oauth2');
//        var_dump($module);exit;

        $accessTokenRepository = $this->getAccessTokenRepository();
        $publicKeyPath = Yii::getAlias($module->publicKey);

        try {
            $server = new ResourceServer(
                $accessTokenRepository,
                $publicKeyPath
            );

            $currentRequest = $module->getRequest();

            $currentRequest = $server->validateAuthenticatedRequest($currentRequest);

            $tokenId = $currentRequest->getAttribute('oauth_access_token_id');

            /** See also \common\models\User::findIdentityByAccessToken  */
            $identity = $user->loginByAccessToken($tokenId, static::class);

            if ($identity === null) {
                throw OAuthServerException::accessDenied('User not found');
            }
            if ((string)$identity->getId() !== (string)$currentRequest->getAttribute('oauth_user_id')) {
                throw OAuthServerException::accessDenied('User ID does not match');
            }


            return $identity;

        } catch (OAuthServerException $e) {
            throw new UnauthorizedHttpException($e);
        } catch (\Exception $e) {
            throw new HttpException(500, 'Unable to validate the request.', 0, YII_DEBUG ? $e : null);
        }


        // profit

    }


    public function getAccessTokenRepository(): AccessTokenRepository
    {
        if (!$this->_accessTokenRepository instanceof AccessTokenRepositoryInterface) {
            $this->_accessTokenRepository = new AccessTokenRepository();
        }
        return $this->_accessTokenRepository;
    }


}