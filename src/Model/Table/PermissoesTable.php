<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Permissoes Model
 *
 * @property \App\Model\Table\PerfisTable&\Cake\ORM\Association\BelongsToMany $Perfis
 * @method \App\Model\Entity\Permisso newEmptyEntity()
 * @method \App\Model\Entity\Permisso newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Permisso> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Permisso get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Permisso findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Permisso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Permisso> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Permisso|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Permisso saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Permisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permisso>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permisso> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permisso>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permisso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permisso> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PermissoesTable extends Table
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

        $this->setTable('permissoes');
        $this->setDisplayField('codigo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Perfis', [
            'foreignKey' => 'permissao_id',
            'targetForeignKey' => 'perfil_id',
            'joinTable' => 'perfis_permissoes',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('codigo')
            ->maxLength('codigo', 120)
            ->requirePresence('codigo', 'create')
            ->notEmptyString('codigo')
            ->add('codigo', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('descricao')
            ->maxLength('descricao', 255)
            ->allowEmptyString('descricao');

        return $validator;
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
        $rules->add($rules->isUnique(['codigo']), ['errorField' => 'codigo']);

        return $rules;
    }
}
