<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cotacoes Model
 *
 * @property \App\Model\Table\SolicitacoesComprasTable&\Cake\ORM\Association\BelongsTo $SolicitacaoCompras
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $Fornecedors
 * @method \App\Model\Entity\Cotaco newEmptyEntity()
 * @method \App\Model\Entity\Cotaco newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Cotaco> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cotaco get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Cotaco findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Cotaco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Cotaco> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cotaco|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Cotaco saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Cotaco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cotaco>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cotaco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cotaco> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cotaco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cotaco>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cotaco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cotaco> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CotacoesTable extends Table
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

        $this->setTable('cotacoes');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SolicitacaoCompras', [
            'foreignKey' => 'solicitacao_compra_id',
            'className' => 'SolicitacoesCompras',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Fornecedors', [
            'foreignKey' => 'fornecedor_id',
            'className' => 'Fornecedores',
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
            ->notEmptyString('solicitacao_compra_id');

        $validator
            ->notEmptyString('fornecedor_id');

        $validator
            ->date('validade')
            ->allowEmptyDate('validade');

        $validator
            ->integer('prazo_entrega')
            ->allowEmptyString('prazo_entrega');

        $validator
            ->scalar('condicoes_pagamento')
            ->allowEmptyString('condicoes_pagamento');

        $validator
            ->decimal('valor_total')
            ->notEmptyString('valor_total');

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
        $rules->add($rules->isUnique(['solicitacao_compra_id', 'fornecedor_id']), ['errorField' => 'solicitacao_compra_id', 'message' => __('This combination of solicitacao_compra_id and fornecedor_id already exists')]);
        $rules->add($rules->existsIn(['solicitacao_compra_id'], 'SolicitacaoCompras'), ['errorField' => 'solicitacao_compra_id']);
        $rules->add($rules->existsIn(['fornecedor_id'], 'Fornecedors'), ['errorField' => 'fornecedor_id']);

        return $rules;
    }
}
