<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VendasItens Model
 *
 * @property \App\Model\Table\VendasTable&\Cake\ORM\Association\BelongsTo $Vendas
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsTo $Produtos
 * @property \App\Model\Table\LotesTable&\Cake\ORM\Association\BelongsTo $Lotes
 * @property \App\Model\Table\ReceitasTable&\Cake\ORM\Association\BelongsTo $Receitas
 * @method \App\Model\Entity\VendasIten newEmptyEntity()
 * @method \App\Model\Entity\VendasIten newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VendasIten> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VendasIten get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VendasIten findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VendasIten patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VendasIten> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VendasIten|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VendasIten saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VendasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VendasIten>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VendasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VendasIten> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VendasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VendasIten>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VendasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VendasIten> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VendasItensTable extends Table
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

        $this->setTable('vendas_itens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Vendas', [
            'foreignKey' => 'venda_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->belongsTo('Produtos', [
            'foreignKey' => 'produto_id',
        ]);
        $this->belongsTo('Lotes', [
            'foreignKey' => 'lote_id',
        ]);
        $this->belongsTo('Receitas', [
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
            ->notEmptyString('venda_id');

        $validator
            ->allowEmptyString('medicamento_id');

        $validator
            ->allowEmptyString('produto_id');

        $validator
            ->allowEmptyString('lote_id');

        $validator
            ->allowEmptyString('receita_id');

        $validator
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmptyString('quantidade');

        $validator
            ->decimal('preco_unitario')
            ->requirePresence('preco_unitario', 'create')
            ->notEmptyString('preco_unitario');

        $validator
            ->decimal('desconto')
            ->notEmptyString('desconto');

        $validator
            ->decimal('total')
            ->requirePresence('total', 'create')
            ->notEmptyString('total');

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
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);
        $rules->add($rules->existsIn(['produto_id'], 'Produtos'), ['errorField' => 'produto_id']);
        $rules->add($rules->existsIn(['lote_id'], 'Lotes'), ['errorField' => 'lote_id']);
        $rules->add($rules->existsIn(['receita_id'], 'Receitas'), ['errorField' => 'receita_id']);

        return $rules;
    }
}
