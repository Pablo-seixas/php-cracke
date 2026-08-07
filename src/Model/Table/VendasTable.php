<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Vendas Model
 *
 * @property \App\Model\Table\ClientesTable&\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\ContasReceberTable&\Cake\ORM\Association\HasMany $ContasReceber
 * @property \App\Model\Table\PagamentosTable&\Cake\ORM\Association\HasMany $Pagamentos
 * @property \App\Model\Table\VendasItensTable&\Cake\ORM\Association\HasMany $VendasItens
 * @method \App\Model\Entity\Venda newEmptyEntity()
 * @method \App\Model\Entity\Venda newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Venda> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Venda get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Venda findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Venda patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Venda> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Venda|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Venda saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Venda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Venda>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Venda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Venda> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Venda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Venda>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Venda>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Venda> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VendasTable extends Table
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

        $this->setTable('vendas');
        $this->setDisplayField('numero');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'cliente_id',
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ContasReceber', [
            'foreignKey' => 'venda_id',
        ]);
        $this->hasMany('Pagamentos', [
            'foreignKey' => 'venda_id',
        ]);
        $this->hasMany('VendasItens', [
            'foreignKey' => 'venda_id',
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
            ->scalar('numero')
            ->maxLength('numero', 40)
            ->requirePresence('numero', 'create')
            ->notEmptyString('numero')
            ->add('numero', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmptyString('cliente_id');

        $validator
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

        $validator
            ->decimal('subtotal')
            ->notEmptyString('subtotal');

        $validator
            ->decimal('desconto')
            ->notEmptyString('desconto');

        $validator
            ->decimal('acrescimo')
            ->notEmptyString('acrescimo');

        $validator
            ->decimal('total')
            ->notEmptyString('total');

        $validator
            ->dateTime('finalizada_em')
            ->allowEmptyDateTime('finalizada_em');

        $validator
            ->dateTime('cancelada_em')
            ->allowEmptyDateTime('cancelada_em');

        $validator
            ->scalar('motivo_cancelamento')
            ->allowEmptyString('motivo_cancelamento');

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
        $rules->add($rules->existsIn(['cliente_id'], 'Clientes'), ['errorField' => 'cliente_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
