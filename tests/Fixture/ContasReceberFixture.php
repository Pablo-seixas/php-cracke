<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContasReceberFixture
 */
class ContasReceberFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'contas_receber';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [];
        parent::init();
    }
}
