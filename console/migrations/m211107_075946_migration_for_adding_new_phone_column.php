<?php

use yii\db\Migration;

/**
 * Class m211107_075946_migration_for_adding_new_phone_column
 */
class m211107_075946_migration_for_adding_new_phone_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'phone_number', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'phone_number');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211107_075946_migration_for_adding_new_phone_column cannot be reverted.\n";

        return false;
    }
    */
}
