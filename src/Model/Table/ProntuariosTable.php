<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Prontuarios Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\ProntuariosHistoricosTable&\Cake\ORM\Association\HasMany $ProntuariosHistoricos
 * @method \App\Model\Entity\Prontuario newEmptyEntity()
 * @method \App\Model\Entity\Prontuario newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Prontuario> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Prontuario get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Prontuario findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Prontuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Prontuario> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Prontuario|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Prontuario saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Prontuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prontuario>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prontuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prontuario> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prontuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prontuario>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prontuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prontuario> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProntuariosTable extends Table
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

        $this->setTable('prontuarios');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ProntuariosHistoricos', [
            'foreignKey' => 'prontuario_id',
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
            ->notEmptyString('paciente_id')
            ->add('paciente_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('historico_clinico')
            ->allowEmptyString('historico_clinico');

        $validator
            ->scalar('alergias')
            ->allowEmptyString('alergias');

        $validator
            ->scalar('medicamentos_uso')
            ->allowEmptyString('medicamentos_uso');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

        $validator
            ->integer('versao')
            ->notEmptyString('versao');

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
        $rules->add($rules->isUnique(['paciente_id']), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);

        return $rules;
    }
}
