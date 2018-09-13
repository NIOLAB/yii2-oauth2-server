<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180517_114315_oauth2
 */
class m180517_114315_oauth2 extends Migration
{
    private $_tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    private static function _tables()
    {
        return [
            '{{%oauth_client}}' => [
                'id' => Schema::TYPE_PK,
                'identifier' => Schema::TYPE_STRING . ' NOT NULL',
                'secret' => Schema::TYPE_STRING, // not confidential if null
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'redirect_uri' => Schema::TYPE_STRING,
                'token_type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Bearer
                'grant_type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Authorization Code
                'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Active,
                'KEY (token_type)',
                'KEY (grant_type)',
                'KEY (status)',
                'KEY (identifier)',
            ],
            '{{%oauth_access_token}}' => [
                'id' => Schema::TYPE_PK,
                'client_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'user_id' => Schema::TYPE_INTEGER,
                'identifier' => Schema::TYPE_STRING . ' NOT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'expired_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Active,
                'FOREIGN KEY (client_id) REFERENCES {{%oauth_client}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'KEY (status)',
                'KEY (identifier)',
            ],
            '{{%oauth_scope}}' => [
                'id' => Schema::TYPE_PK,
                'identifier' => Schema::TYPE_STRING . ' NOT NULL',
                'name' => Schema::TYPE_STRING,
                'KEY (identifier)',
            ],
            '{{%oauth_client_scope}}' => [
                'id' => Schema::TYPE_PK,
                'client_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'scope_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'user_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL', // common if null
                'grant_type' => Schema::TYPE_SMALLINT . ' DEFAULT NULL', // all grants if null
                'is_default' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
                'UNIQUE KEY (client_id, scope_id, user_id, grant_type)',
                'FOREIGN KEY (client_id) REFERENCES {{%oauth_client}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'FOREIGN KEY (scope_id) REFERENCES {{%oauth_scope}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'KEY (grant_type)',
                'KEY (is_default)',
            ],
            '{{%oauth_access_token_scope}}' => [
                'access_token_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'scope_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'PRIMARY KEY (access_token_id, scope_id)',
                'FOREIGN KEY (access_token_id) REFERENCES {{%oauth_access_token}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'FOREIGN KEY (scope_id) REFERENCES {{%oauth_scope}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
            ],
            '{{%oauth_refresh_token}}' => [
                'id' => Schema::TYPE_PK,
                'access_token_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'identifier' => Schema::TYPE_STRING . ' NOT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'expired_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Active,
                'FOREIGN KEY (access_token_id) REFERENCES {{%oauth_access_token}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'KEY (status)',
                'KEY (identifier)',
            ],
            '{{%oauth_auth_code}}' => [
                'id' => Schema::TYPE_PK,
                'identifier' => Schema::TYPE_STRING . ' NOT NULL',
                'client_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'user_id' => Schema::TYPE_INTEGER,
                'created_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'expired_at' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1', // Active,
                'FOREIGN KEY (client_id) REFERENCES {{%oauth_client}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'KEY (status)',
                'KEY (identifier)',
            ],
            '{{%oauth_auth_code_scope}}' => [
                'auth_code_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'scope_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'PRIMARY KEY (auth_code_id, scope_id)',
                'FOREIGN KEY (auth_code_id) REFERENCES {{%oauth_auth_code}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
                'FOREIGN KEY (scope_id) REFERENCES {{%oauth_scope}} (id) ON DELETE CASCADE ON UPDATE CASCADE',
            ],
        ];
    }

    public function safeUp()
    {
        foreach (static::_tables() as $name => $attributes) {
            try {
                $this->createTable($name, $attributes, $this->_tableOptions);
            } catch (\Exception $e) {
                echo $e->getMessage(), "\n";
                return false;
            }
        }

        return true;
    }

    public function safeDown()
    {
        foreach (array_reverse(static::_tables()) as $name => $attributes) {
            try {
                $this->dropTable($name);
            } catch (\Exception $e) {
                echo "m160920_072449_oauth cannot be reverted.\n";
                echo $e->getMessage(), "\n";
                return false;
            }
        }

        return true;
    }
}