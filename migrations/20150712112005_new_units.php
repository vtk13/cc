<?php

use Phinx\Migration\AbstractMigration;

class NewUnits extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO units VALUES(4, 'Пакетик')");
    }

    public function down()
    {
        $this->execute("DELETE FROM units WHERE id=4");
    }
}
