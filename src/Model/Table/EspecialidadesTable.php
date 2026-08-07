<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Especialidades Model
 *
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\HasMany $Profissionais
 * @method \App\Model\Entity\Especialidade newEmptyEntity()
 * @method \App\Model\Entity\Especialidade newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Especialidade> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Especialidade get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Especialidade findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Especialidade patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Especialidade> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Especialidade|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Especialidade saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Especialidade>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Especialidade>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Especialidade>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Especialidade> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Especialidade>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Especialidade>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Especialidade>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Especialidade> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EspecialidadesTable extends Table
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

        $this->setTable('especialidades');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Profissionais', [
            'foreignKey' => 'especialidade_id',
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
            ->scalar('nome')
            ->maxLength('nome', 120)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome')
            ->add('nome', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->boolean('ativo')
            ->notEmptyString('ativo');

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
        $rules->add($rules->isUnique(['nome']), ['errorField' => 'nome']);

        return $rules;
    }
}
