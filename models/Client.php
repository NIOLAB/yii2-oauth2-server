<?php
/**
 * Created by PhpStorm.
 * User: Harry
 * Date: 15-5-2018
 * Time: 16:46
 *
 * Client = andere applicatie die connectie maakt met ons als oauth2 server
 */

namespace NIOLAB\oauth2\models;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oauth_client".
 *
 * @property int $id
 * @property string $identifier
 * @property string $secret
 * @property string $name
 * @property string $redirect_uri
 * @property int $token_type
 * @property int $is_confidential
 * @property int $grant_type
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 *
 * @property Scope[] $scopes
 */
class Client extends ActiveRecord implements ClientEntityInterface {


    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = -1;

    const GRANT_TYPE_CLIENT_CREDENTIALS = 1;
    const GRANT_TYPE_PASSWORD = 2;

    const GRANT_TYPE_AUTHORIZATION_CODE = 3;
    const GRANT_TYPE_IMPLICIT = 4;
    const GRANT_TYPE_REFRESH_TOKEN = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oauth_client}}';
    }

    public static function getGrantTypeOptions()
    {
        return [
            static::GRANT_TYPE_AUTHORIZATION_CODE => 'authorization_code',
            static::GRANT_TYPE_IMPLICIT => 'implicit',
            static::GRANT_TYPE_PASSWORD => 'password',
            static::GRANT_TYPE_CLIENT_CREDENTIALS => 'client_credentials',
            static::GRANT_TYPE_REFRESH_TOKEN => 'refresh_token',
        ];
    }

    public static function getGrantTypeId($grantType, $default = null)
    {
        return ArrayHelper::getValue(array_flip(static::getGrantTypeOptions()), $grantType, $default);
    }

    /**
    * @inheritdoc
    */
    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string|string[]
     */
    public function getRedirectUri() {
        return $this->redirect_uri;
    }

    /**
     * @param $secret
     * @return bool
     */
    public function validateSecret($secret) {
        return password_verify($secret,$this->secret);
    }


    public function hashSecret($secret) {
        return password_hash($secret,PASSWORD_DEFAULT);
    }

    public function attributeLabels() {
        return [
            'identifier' => Yii::t('oauth2','Client ID'),
            'secret' => Yii::t('oauth2','Client secret'),
            'is_confidential' => Yii::t('oauth2','Can the client store secrets? (Confidential client)'),
        ];
    }



    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['identifier','secret','name','redirect_uri'], 'required'],
            [['is_confidential'],'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->secret = $this->hashSecret($this->secret);
        }
        return parent::beforeSave($insert);
    }


    /**
     * @inheritdoc
     */
    public function getScopes(callable $filter)
    {
        return $this->hasMany(Scope::class, ['id' => 'scope_id'])
            ->viaTable('{{%oauth_client_scope}}', ['client_id' => 'id'], $filter);
    }


    /**
     * @inheritdoc
     */
    public function isConfidential()
    {
        return (bool)$this->is_confidential;
    }
}