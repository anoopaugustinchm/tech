<?php

use yii\db\Migration;

/**
 * Class m211107_075222_migration_for_adding_new_roles_column
 */
class m211107_075222_migration_for_adding_new_roles_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211107_075222_migration_for_adding_new_roles_column cannot be reverted.\n";

        return false;
    }
    */
}
