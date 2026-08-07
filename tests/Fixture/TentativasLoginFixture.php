<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TentativasLoginFixture
 */
class TentativasLoginFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'tentativas_login';
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
