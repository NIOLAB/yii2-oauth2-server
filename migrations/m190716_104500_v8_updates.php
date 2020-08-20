<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m190716_104500_v8_updates
 */
class m190716_104500_v8_updates extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%oauth_client}}','is_confidential',$this->boolean()->notNull()->defaultValue(1).' AFTER `token_type`');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%oauth_client}}','is_confidential');
    }

}