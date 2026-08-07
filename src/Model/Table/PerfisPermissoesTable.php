<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * PerfisPermissoes Model
 *
 * @property \App\Model\Table\PerfisTable&\Cake\ORM\Association\BelongsTo $Perfils
 * @property \App\Model\Table\PermissoesTable&\Cake\ORM\Association\BelongsTo $Permissaos
 * @method \App\Model\Entity\PerfisPermisso newEmptyEntity()
 * @method \App\Model\Entity\PerfisPermisso newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PerfisPermisso> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PerfisPermisso get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PerfisPermisso findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PerfisPermisso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PerfisPermisso> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PerfisPermisso|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PerfisPermisso saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PerfisPermisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PerfisPermisso>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PerfisPermisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PerfisPermisso> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PerfisPermisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PerfisPermisso>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PerfisPermisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PerfisPermisso> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PerfisPermissoesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('perfis_permissoes');
        $this->setDisplayField(['perfil_id', 'permissao_id']);
        $this->setPrimaryKey(['perfil_id', 'permissao_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Perfis', [
            'foreignKey' => 'perfil_id',
            'className' => 'Perfis',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Permissoes', [
            'foreignKey' => 'permissao_id',
            'className' => 'Permissoes',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['perfil_id'], 'Perfis'), ['errorField' => 'perfil_id']);
        $rules->add($rules->existsIn(['permissao_id'], 'Permissoes'), ['errorField' => 'permissao_id']);

        return $rules;
    }
}
