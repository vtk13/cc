<?php

use Phinx\Migration\AbstractMigration;

class Categories extends AbstractMigration
{
    public function up()
    {
        $this->table('goods')
            ->addColumn('node_left', 'integer')
            ->addColumn('node_right', 'integer')
            ->addColumn('level', 'integer')
            ->save();

        $this->execute('SET @n = 0');
        $this->execute('UPDATE goods SET node_left=(@n:=@n+1), node_right=(@n:=@n+1)');
    }

    public function down()
    {
        $this->table('goods')
            ->removeColumn('node_left', 'integer')
            ->removeColumn('node_right', 'integer')
            ->removeColumn('level', 'integer')
            ->save();
    }
}
