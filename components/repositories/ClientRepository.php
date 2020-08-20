<?php
/**
 * Created by PhpStorm.
 * User: Harry
 * Date: 15-5-2018
 * Time: 16:21
 */

namespace NIOLAB\oauth2\components\repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use NIOLAB\oauth2\models\Client;

class ClientRepository implements \League\OAuth2\Server\Repositories\ClientRepositoryInterface {



    /**
     * Validate a client's secret.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $clientSecret The client's secret (if sent)
     * @param null|string $grantType The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        /* @TODO do something with $grantType ?? */

        /** @var Client $client */
        $client = Client::find()->where(['status'=>Client::STATUS_ACTIVE,'identifier'=>$clientIdentifier])->one();

        if ($client instanceof Client) {
            $isValidUser = $client->validateSecret($clientSecret);
            return $isValidUser;
        }

        return false;
    }

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier)
    {
        /** @var Client $client */
        $client = Client::find()->where(['status'=>Client::STATUS_ACTIVE,'identifier'=>$clientIdentifier])->one();
        return $client;
    }

}