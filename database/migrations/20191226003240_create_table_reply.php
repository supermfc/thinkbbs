<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateTableReply extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('reply',array('engine'=>'InnoDB'));
        $table->addColumn('topic_id', 'integer', array('default' => 0, 'signed' => false, 'null' => false, 'comment' => '话题ID'))
            ->addColumn('user_id', 'integer', array('default' => 0, 'signed' => false, 'null' => false, 'comment' => '用户ID'))
            ->addColumn('content', 'text', array('comment' => '评论内容'))
            ->addColumn('create_time', 'integer', array('default' => 0, 'signed' => false, 'null' => false))
            ->addColumn('update_time', 'integer', array('default' => 0, 'signed' => false, 'null' => false))
            ->addIndex(['topic_id'], ['name' => 'idx_by_topic_id'])
            ->addIndex(['user_id', 'topic_id'], ['name' => 'idx_by_user_id_and_topic_id'])
            ->save();
    }

    public function down()
    {
        $this->dropTable('reply');
    }
}
