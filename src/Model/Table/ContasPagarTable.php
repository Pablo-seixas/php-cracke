<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContasPagar Model
 *
 * @property \App\Model\Table\ComprasTable&\Cake\ORM\Association\BelongsTo $Compras
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $Fornecedors
 * @method \App\Model\Entity\ContasPagar newEmptyEntity()
 * @method \App\Model\Entity\ContasPagar newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ContasPagar> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContasPagar get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ContasPagar findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ContasPagar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ContasPagar> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContasPagar|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ContasPagar saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ContasPagar>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasPagar>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasPagar>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasPagar> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasPagar>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasPagar>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ContasPagar>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ContasPagar> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContasPagarTable extends Table
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

        $this->setTable('contas_pagar');
        $this->setDisplayField('descricao');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Compras', [
            'foreignKey' => 'compra_id',
        ]);
        $this->belongsTo('Fornecedores', [
            'foreignKey' => 'fornecedor_id',
            'className' => 'Fornecedores',
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
            ->allowEmptyString('compra_id');

        $validator
            ->allowEmptyString('fornecedor_id');

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
            ->dateTime('pago_em')
            ->allowEmptyDateTime('pago_em');

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
        $rules->add($rules->existsIn(['compra_id'], 'Compras'), ['errorField' => 'compra_id']);
        $rules->add($rules->existsIn(['fornecedor_id'], 'Fornecedores'), ['errorField' => 'fornecedor_id']);

        return $rules;
    }
}
