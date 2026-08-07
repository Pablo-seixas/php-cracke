<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContasReceber Model
 *
 * @property \App\Model\Table\VendasTable&\Cake\ORM\Association\BelongsTo $Vendas
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @method \App\Model\Entity\ContasReceber newEmptyEntity()
 * @method \App\Model\Entity\ContasReceber newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ContasReceber> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContasReceber get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ContasReceber findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ContasReceber patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ContasReceber> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContasReceber|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ContasReceber saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ContasReceber>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasReceber>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasReceber>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasReceber> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasReceber>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasReceber>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasReceber>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasReceber> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContasReceberTable extends Table
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

        $this->setTable('contas_receber');
        $this->setDisplayField('descricao');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Vendas', [
            'foreignKey' => 'venda_id',
        ]);
        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
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
            ->allowEmptyString('venda_id');

        $validator
            ->allowEmptyString('paciente_id');

        $validator
            ->scalar('descricao')
            ->maxLength('descricao', 255)
            ->requirePresence('descricao', 'create')
            ->notEmptyString('descricao');

        $validator
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

        $validator
            ->date('vencimento')
            ->requirePresence('vencimento', 'create')
            ->notEmptyDate('vencimento');

        $validator
            ->integer('parcela')
            ->notEmptyString('parcela');

        $validator
            ->integer('total_parcelas')
            ->notEmptyString('total_parcelas');

        $validator
            ->decimal('juros')
            ->notEmptyString('juros');

        $validator
            ->decimal('multa')
            ->notEmptyString('multa');

        $validator
            ->decimal('desconto')
            ->notEmptyString('desconto');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

        $validator
            ->dateTime('recebido_em')
            ->allowEmptyDateTime('recebido_em');

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
        $rules->add($rules->existsIn(['venda_id'], 'Vendas'), ['errorField' => 'venda_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);

        return $rules;
    }
}
