<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Prescricoes Model
 *
 * @property \App\Model\Table\AtendimentosTable&\Cake\ORM\Association\BelongsTo $Atendimentos
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\BelongsTo $Profissionals
 * @method \App\Model\Entity\Prescrico newEmptyEntity()
 * @method \App\Model\Entity\Prescrico newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Prescrico> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Prescrico get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Prescrico findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Prescrico patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Prescrico> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Prescrico|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Prescrico saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Prescrico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prescrico>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prescrico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prescrico> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prescrico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prescrico>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prescrico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prescrico> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PrescricoesTable extends Table
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

        $this->setTable('prescricoes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Atendimentos', [
            'foreignKey' => 'atendimento_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Profissionais', [
            'foreignKey' => 'profissional_id',
            'className' => 'Profissionais',
            'joinType' => 'INNER',
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
            ->notEmptyString('atendimento_id');

        $validator
            ->notEmptyString('profissional_id');

        $validator
            ->scalar('conteudo')
            ->requirePresence('conteudo', 'create')
            ->notEmptyString('conteudo');

        $validator
            ->date('validade')
            ->allowEmptyDate('validade');

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
        $rules->add($rules->existsIn(['atendimento_id'], 'Atendimentos'), ['errorField' => 'atendimento_id']);
        $rules->add($rules->existsIn(['profissional_id'], 'Profissionais'), ['errorField' => 'profissional_id']);

        return $rules;
    }
}
