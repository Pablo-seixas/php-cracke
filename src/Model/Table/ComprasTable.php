<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Compras Model
 *
 * @property \App\Model\Table\SolicitacoesComprasTable&\Cake\ORM\Association\BelongsTo $SolicitacaoCompras
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $Fornecedors
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\ComprasItensTable&\Cake\ORM\Association\HasMany $ComprasItens
 * @property \App\Model\Table\ContasPagarTable&\Cake\ORM\Association\HasMany $ContasPagar
 * @method \App\Model\Entity\Compra newEmptyEntity()
 * @method \App\Model\Entity\Compra newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Compra> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Compra get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Compra findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Compra patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Compra> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Compra|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Compra saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Compra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Compra>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Compra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Compra> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Compra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Compra>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Compra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Compra> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ComprasTable extends Table
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

        $this->setTable('compras');
        $this->setDisplayField('numero');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SolicitacaoCompras', [
            'foreignKey' => 'solicitacao_compra_id',
            'className' => 'SolicitacoesCompras',
        ]);
        $this->belongsTo('Fornecedores', [
            'foreignKey' => 'fornecedor_id',
            'className' => 'Fornecedores',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ComprasItens', [
            'foreignKey' => 'compra_id',
        ]);
        $this->hasMany('ContasPagar', [
            'foreignKey' => 'compra_id',
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
            ->allowEmptyString('solicitacao_compra_id');

        $validator
            ->notEmptyString('fornecedor_id');

        $validator
            ->scalar('numero')
            ->maxLength('numero', 40)
            ->requirePresence('numero', 'create')
            ->notEmptyString('numero')
            ->add('numero', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('nota_fiscal')
            ->maxLength('nota_fiscal', 80)
            ->allowEmptyString('nota_fiscal');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

        $validator
            ->decimal('valor_total')
            ->notEmptyString('valor_total');

        $validator
            ->dateTime('emitida_em')
            ->notEmptyDateTime('emitida_em');

        $validator
            ->dateTime('recebida_em')
            ->allowEmptyDateTime('recebida_em');

        $validator
            ->notEmptyString('usuario_id');

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
        $rules->add($rules->existsIn(['solicitacao_compra_id'], 'SolicitacaoCompras'), ['errorField' => 'solicitacao_compra_id']);
        $rules->add($rules->existsIn(['fornecedor_id'], 'Fornecedores'), ['errorField' => 'fornecedor_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
