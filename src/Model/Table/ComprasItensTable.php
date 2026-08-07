<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ComprasItens Model
 *
 * @property \App\Model\Table\ComprasTable&\Cake\ORM\Association\BelongsTo $Compras
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsTo $Produtos
 * @method \App\Model\Entity\ComprasIten newEmptyEntity()
 * @method \App\Model\Entity\ComprasIten newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ComprasIten> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ComprasIten get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ComprasIten findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ComprasIten patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ComprasIten> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ComprasIten|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ComprasIten saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ComprasIten>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ComprasIten> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ComprasIten>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ComprasIten> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ComprasItensTable extends Table
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

        $this->setTable('compras_itens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Compras', [
            'foreignKey' => 'compra_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->belongsTo('Produtos', [
            'foreignKey' => 'produto_id',
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
            ->notEmptyString('compra_id');

        $validator
            ->allowEmptyString('medicamento_id');

        $validator
            ->allowEmptyString('produto_id');

        $validator
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmptyString('quantidade');

        $validator
            ->decimal('quantidade_recebida')
            ->notEmptyString('quantidade_recebida');

        $validator
            ->decimal('preco_unitario')
            ->requirePresence('preco_unitario', 'create')
            ->notEmptyString('preco_unitario');

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
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);
        $rules->add($rules->existsIn(['produto_id'], 'Produtos'), ['errorField' => 'produto_id']);

        return $rules;
    }
}
