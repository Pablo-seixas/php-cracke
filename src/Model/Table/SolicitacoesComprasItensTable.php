<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SolicitacoesComprasItens Model
 *
 * @property \App\Model\Table\SolicitacoesComprasTable&\Cake\ORM\Association\BelongsTo $SolicitacaoCompras
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsTo $Produtos
 * @method \App\Model\Entity\SolicitacoesComprasIten newEmptyEntity()
 * @method \App\Model\Entity\SolicitacoesComprasIten newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SolicitacoesComprasIten> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SolicitacoesComprasIten get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SolicitacoesComprasIten findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SolicitacoesComprasIten patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SolicitacoesComprasIten> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SolicitacoesComprasIten|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SolicitacoesComprasIten saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesComprasIten>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesComprasIten> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesComprasIten>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesComprasIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesComprasIten> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SolicitacoesComprasItensTable extends Table
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

        $this->setTable('solicitacoes_compras_itens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SolicitacaoCompras', [
            'foreignKey' => 'solicitacao_compra_id',
            'className' => 'SolicitacoesCompras',
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
            ->notEmptyString('solicitacao_compra_id');

        $validator
            ->allowEmptyString('medicamento_id');

        $validator
            ->allowEmptyString('produto_id');

        $validator
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmptyString('quantidade');

        $validator
            ->decimal('preco_estimado')
            ->allowEmptyString('preco_estimado');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

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
        $rules->add($rules->existsIn(['solicitacao_compra_id'], 'SolicitacaoCompras'), ['errorField' => 'solicitacao_compra_id']);
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);
        $rules->add($rules->existsIn(['produto_id'], 'Produtos'), ['errorField' => 'produto_id']);

        return $rules;
    }
}
