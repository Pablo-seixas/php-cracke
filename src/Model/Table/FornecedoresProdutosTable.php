<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FornecedoresProdutos Model
 *
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $Fornecedors
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsTo $Produtos
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @method \App\Model\Entity\FornecedoresProduto newEmptyEntity()
 * @method \App\Model\Entity\FornecedoresProduto newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FornecedoresProduto> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FornecedoresProduto get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FornecedoresProduto findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FornecedoresProduto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FornecedoresProduto> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FornecedoresProduto|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FornecedoresProduto saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FornecedoresProduto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FornecedoresProduto>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FornecedoresProduto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FornecedoresProduto> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FornecedoresProduto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FornecedoresProduto>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FornecedoresProduto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FornecedoresProduto> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FornecedoresProdutosTable extends Table
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

        $this->setTable('fornecedores_produtos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Fornecedors', [
            'foreignKey' => 'fornecedor_id',
            'className' => 'Fornecedores',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Produtos', [
            'foreignKey' => 'produto_id',
        ]);
        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
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
            ->notEmptyString('fornecedor_id');

        $validator
            ->allowEmptyString('produto_id');

        $validator
            ->allowEmptyString('medicamento_id');

        $validator
            ->scalar('codigo_fornecedor')
            ->maxLength('codigo_fornecedor', 80)
            ->allowEmptyString('codigo_fornecedor');

        $validator
            ->decimal('ultimo_preco')
            ->allowEmptyString('ultimo_preco');

        $validator
            ->integer('prazo_entrega')
            ->allowEmptyString('prazo_entrega');

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
        $rules->add($rules->isUnique(['fornecedor_id', 'produto_id', 'medicamento_id'], ['allowMultipleNulls' => true]), ['errorField' => 'fornecedor_id', 'message' => __('This combination of fornecedor_id, produto_id and medicamento_id already exists')]);
        $rules->add($rules->existsIn(['fornecedor_id'], 'Fornecedors'), ['errorField' => 'fornecedor_id']);
        $rules->add($rules->existsIn(['produto_id'], 'Produtos'), ['errorField' => 'produto_id']);
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);

        return $rules;
    }
}
