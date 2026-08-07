<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Agendas Model
 *
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\BelongsTo $Profissionals
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\HasMany $Consultas
 * @method \App\Model\Entity\Agenda newEmptyEntity()
 * @method \App\Model\Entity\Agenda newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Agenda> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Agenda get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Agenda findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Agenda patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Agenda> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Agenda|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Agenda saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Agenda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Agenda>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Agenda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Agenda> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Agenda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Agenda>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Agenda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Agenda> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AgendasTable extends Table
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

        $this->setTable('agendas');
        $this->setDisplayField('tipo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Profissionals', [
            'foreignKey' => 'profissional_id',
            'className' => 'Profissionais',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Consultas', [
            'foreignKey' => 'agenda_id',
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
            ->notEmptyString('profissional_id');

        $validator
            ->dateTime('inicio')
            ->requirePresence('inicio', 'create')
            ->notEmptyDateTime('inicio');

        $validator
            ->dateTime('fim')
            ->requirePresence('fim', 'create')
            ->notEmptyDateTime('fim');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 20)
            ->notEmptyString('tipo');

        $validator
            ->scalar('motivo')
            ->allowEmptyString('motivo');

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
        $rules->add($rules->isUnique(['profissional_id', 'inicio']), ['errorField' => 'profissional_id', 'message' => __('This combination of profissional_id and inicio already exists')]);
        $rules->add($rules->existsIn(['profissional_id'], 'Profissionals'), ['errorField' => 'profissional_id']);

        return $rules;
    }
}
