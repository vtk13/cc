<?php
use Phinx\Migration\AbstractMigration;

class BasicSchema extends AbstractMigration
{

    public function up()
    {
        $this->table('units')
            ->addColumn('title', 'string')
            ->create();

        $this->execute("INSERT INTO units VALUES (1, 'кг')");
        $this->execute("INSERT INTO units VALUES (2,  'л')");
        $this->execute("INSERT INTO units VALUES (3, 'шт')");

        $this->table('goods')
            ->addColumn('bar_code', 'string')
            ->addColumn('title', 'string')
            ->addColumn('unit', 'integer')
            ->create();

        $this->table('shops')
            ->addColumn('address_id', 'string')
            ->addColumn('title', 'string')
            ->create();

        $this->table('sales')
            ->addColumn('good_id', 'integer')
            ->addColumn('shop_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('timestamp', 'integer')
            ->addColumn('cost', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 3])
            ->create();
    }

    public function down()
    {
        $this->table('units')->drop();
        $this->table('goods')->drop();
        $this->table('shops')->drop();
        $this->table('sales')->drop();
    }
}
