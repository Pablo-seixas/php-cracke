<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CotacoesItens Model
 *
 * @property \App\Model\Table\CotacoesTable&\Cake\ORM\Association\BelongsTo $Cotacaos
 * @property \App\Model\Table\SolicitacoesComprasItensTable&\Cake\ORM\Association\BelongsTo $SolicitacaoCompraItems
 * @method \App\Model\Entity\CotacoesIten newEmptyEntity()
 * @method \App\Model\Entity\CotacoesIten newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CotacoesIten> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CotacoesIten get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CotacoesIten findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CotacoesIten patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CotacoesIten> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CotacoesIten|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CotacoesIten saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CotacoesIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CotacoesIten>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CotacoesIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CotacoesIten> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CotacoesIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CotacoesIten>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CotacoesIten>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CotacoesIten> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CotacoesItensTable extends Table
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

        $this->setTable('cotacoes_itens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cotacoes', [
            'foreignKey' => 'cotacao_id',
            'className' => 'Cotacoes',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SolicitacoesComprasItens', [
            'foreignKey' => 'solicitacao_compra_item_id',
            'className' => 'SolicitacoesComprasItens',
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
            ->notEmptyString('cotacao_id');

        $validator
            ->notEmptyString('solicitacao_compra_item_id');

        $validator
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmptyString('quantidade');

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
        $rules->add($rules->isUnique(['cotacao_id', 'solicitacao_compra_item_id']), ['errorField' => 'cotacao_id', 'message' => __('This combination of cotacao_id and solicitacao_compra_item_id already exists')]);
        $rules->add($rules->existsIn(['cotacao_id'], 'Cotacoes'), ['errorField' => 'cotacao_id']);
        $rules->add($rules->existsIn(['solicitacao_compra_item_id'], 'SolicitacoesComprasItens'), ['errorField' => 'solicitacao_compra_item_id']);

        return $rules;
    }
}
