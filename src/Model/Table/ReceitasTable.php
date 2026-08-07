<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Receitas Model
 *
 * @property \App\Model\Table\PrescricoesTable&\Cake\ORM\Association\BelongsTo $Prescricaos
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\BelongsTo $Profissionals
 * @property \App\Model\Table\VendasItensTable&\Cake\ORM\Association\HasMany $VendasItens
 * @method \App\Model\Entity\Receita newEmptyEntity()
 * @method \App\Model\Entity\Receita newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Receita> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Receita get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Receita findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Receita patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Receita> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Receita|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Receita saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Receita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receita>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receita> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receita>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receita> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReceitasTable extends Table
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

        $this->setTable('receitas');
        $this->setDisplayField('numero');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Prescricaos', [
            'foreignKey' => 'prescricao_id',
            'className' => 'Prescricoes',
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
        $this->hasMany('VendasItens', [
            'foreignKey' => 'receita_id',
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
            ->allowEmptyString('prescricao_id');

        $validator
            ->notEmptyString('paciente_id');

        $validator
            ->notEmptyString('profissional_id');

        $validator
            ->scalar('numero')
            ->maxLength('numero', 80)
            ->requirePresence('numero', 'create')
            ->notEmptyString('numero')
            ->add('numero', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 50)
            ->allowEmptyString('tipo');

        $validator
            ->dateTime('emitida_em')
            ->requirePresence('emitida_em', 'create')
            ->notEmptyDateTime('emitida_em');

        $validator
            ->date('validade')
            ->allowEmptyDate('validade');

        $validator
            ->scalar('arquivo_path')
            ->maxLength('arquivo_path', 255)
            ->allowEmptyString('arquivo_path');

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
        $rules->add($rules->isUnique(['numero']), ['errorField' => 'numero']);
        $rules->add($rules->existsIn(['prescricao_id'], 'Prescricaos'), ['errorField' => 'prescricao_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['profissional_id'], 'Profissionals'), ['errorField' => 'profissional_id']);

        return $rules;
    }
}
