<?php
use Phinx\Migration\AbstractMigration;

class GoodsUnits extends AbstractMigration
{
    public function up()
    {
        $this->table('goods')
            ->addColumn('packed', 'integer')
            ->addColumn('pack_volume', 'decimal', ['precision' => 10, 'scale' => 2])
            ->save();
    }

    public function down()
    {
        $this->table('goods')
            ->removeColumn('packed')
            ->removeColumn('pack_volume')
            ->save();
    }
}
