<?php

use Phinx\Migration\AbstractMigration;

class ChangePackVolumePrecision extends AbstractMigration
{
    public function up()
    {
        $this->table('goods')
            ->changeColumn('pack_volume', 'decimal', ['precision' => 10, 'scale' => 3])
            ->save();
    }

    public function down()
    {
        $this->table('goods')
            ->changeColumn('pack_volume', 'decimal', ['precision' => 10, 'scale' => 2])
            ->save();
    }
}
