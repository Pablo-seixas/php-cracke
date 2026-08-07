<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Atendimentos Model
 *
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\BelongsTo $Profissionals
 * @property \App\Model\Table\PrescricoesTable&\Cake\ORM\Association\HasMany $Prescricoes
 * @method \App\Model\Entity\Atendimento newEmptyEntity()
 * @method \App\Model\Entity\Atendimento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Atendimento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Atendimento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Atendimento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Atendimento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Atendimento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Atendimento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Atendimento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Atendimento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Atendimento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Atendimento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Atendimento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Atendimento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Atendimento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Atendimento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Atendimento> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AtendimentosTable extends Table
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

        $this->setTable('atendimentos');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Consultas', [
            'foreignKey' => 'consulta_id',
        ]);
        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Profissionals', [
            'foreignKey' => 'profissional_id',
            'className' => 'Profissionais',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Prescricoes', [
            'foreignKey' => 'atendimento_id',
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
            ->allowEmptyString('consulta_id')
            ->add('consulta_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->notEmptyString('paciente_id');

        $validator
            ->notEmptyString('profissional_id');

        $validator
            ->dateTime('iniciado_em')
            ->requirePresence('iniciado_em', 'create')
            ->notEmptyDateTime('iniciado_em');

        $validator
            ->dateTime('finalizado_em')
            ->allowEmptyDateTime('finalizado_em');

        $validator
            ->scalar('motivo')
            ->allowEmptyString('motivo');

        $validator
            ->scalar('sintomas')
            ->allowEmptyString('sintomas');

        $validator
            ->scalar('pressao_arterial')
            ->maxLength('pressao_arterial', 20)
            ->allowEmptyString('pressao_arterial');

        $validator
            ->decimal('temperatura')
            ->allowEmptyString('temperatura');

        $validator
            ->decimal('peso')
            ->allowEmptyString('peso');

        $validator
            ->decimal('altura')
            ->allowEmptyString('altura');

        $validator
            ->decimal('glicemia')
            ->allowEmptyString('glicemia');

        $validator
            ->decimal('saturacao')
            ->allowEmptyString('saturacao');

        $validator
            ->scalar('orientacoes')
            ->allowEmptyString('orientacoes');

        $validator
            ->scalar('encaminhamentos')
            ->allowEmptyString('encaminhamentos');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

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
        $rules->add($rules->isUnique(['consulta_id'], ['allowMultipleNulls' => true]), ['errorField' => 'consulta_id']);
        $rules->add($rules->existsIn(['consulta_id'], 'Consultas'), ['errorField' => 'consulta_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['profissional_id'], 'Profissionals'), ['errorField' => 'profissional_id']);

        return $rules;
    }
}
