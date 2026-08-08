<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Consultas Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\BelongsTo $Profissionals
 * @property \App\Model\Table\AgendasTable&\Cake\ORM\Association\BelongsTo $Agendas
 * @property \App\Model\Table\AtendimentosTable&\Cake\ORM\Association\HasOne $Atendimentos
 * @property \App\Model\Table\PagamentosTable&\Cake\ORM\Association\HasMany $Pagamentos
 * @method \App\Model\Entity\Consulta newEmptyEntity()
 * @method \App\Model\Entity\Consulta newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Consulta> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Consulta get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Consulta findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Consulta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Consulta> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Consulta|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Consulta saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConsultasTable extends Table
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

        $this->setTable('consultas');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Profissionais', [
            'foreignKey' => 'profissional_id',
            'className' => 'Profissionais',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Agendas', [
            'foreignKey' => 'agenda_id',
        ]);
        $this->hasOne('Atendimentos', [
            'foreignKey' => 'consulta_id',
        ]);
        $this->hasMany('Pagamentos', [
            'foreignKey' => 'consulta_id',
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
            ->notEmptyString('paciente_id');

        $validator
            ->notEmptyString('profissional_id');

        $validator
            ->allowEmptyString('agenda_id');

        $validator
            ->dateTime('inicio')
            ->requirePresence('inicio', 'create')
            ->notEmptyDateTime('inicio');

        $validator
            ->dateTime('fim')
            ->requirePresence('fim', 'create')
            ->notEmptyDateTime('fim');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

        $validator
            ->scalar('motivo')
            ->allowEmptyString('motivo');

        $validator
            ->dateTime('confirmado_em')
            ->allowEmptyDateTime('confirmado_em');

        $validator
            ->dateTime('cancelado_em')
            ->allowEmptyDateTime('cancelado_em');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

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
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['profissional_id'], 'Profissionais'), ['errorField' => 'profissional_id']);
        $rules->add($rules->existsIn(['agenda_id'], 'Agendas'), ['errorField' => 'agenda_id']);

        return $rules;
    }
}
