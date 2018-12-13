Extension for Yii2 providing an oAuth 2 server
================================

Uses parts of [https://github.com/samdark/yii2-league-oauth2-server](https://github.com/samdark/yii2-league-oauth2-server)

Also inspired by [https://github.com/chervand/yii2-oauth2-server](https://github.com/chervand/yii2-oauth2-server)

## Install
Add this to your `composer.json`:
```json

"NIOLAB/yii2-oauth2-server": "@dev"

```

## Usage

### Step 1
You need a few things:

- A UserRepository for this module to get its users from. The easiest is to take your existing `User` class, and make sure it also implements the following interfaces:
  - `League\OAuth2\Server\Entities\UserEntityInterface`
  - `League\OAuth2\Server\Repositories\UserRepositoryInterface`
      - Make sure to *validate* the user in `UserRepositoryInterface::getUserEntityByUserCredentials()`
  
  And then pass the User class as the property `$userRepository` in the configuration array as below.

- An SSH key pair. See [https://oauth2.thephpleague.com/installation/]()

Make sure the file rights are 600 or 660 for the generated key files.

- An encryption key (just a random string)

- The migrations

```bash
$ php yii migrate --migrationPath=@vendor/NIOLAB/yii2-oauth2-server/migrations
```

### Step 2
Add it as a yii2 module:
```php
<?php
$config = [
 'modules' => [
        'oauth2' => [
            'class' => NIOLAB\oauth2\Module::class,
            'userRepository' => \app\models\User::class,
            'privateKey' => '@common/data/keys/private.key',
            'publicKey' => '@common/data/keys/public.key',
            'encryptionKey' => 'put-a-nice-random-string-here',
        ],
    ],
];
?>
```

## Configuration
There's not a lot of configuration yet. Maybe the types of grants available will be dynamic someday.

