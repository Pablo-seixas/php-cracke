<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Lotes Model
 *
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $Fornecedors
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\MovimentacoesEstoqueTable&\Cake\ORM\Association\HasMany $MovimentacoesEstoque
 * @property \App\Model\Table\VendasItensTable&\Cake\ORM\Association\HasMany $VendasItens
 * @method \App\Model\Entity\Lote newEmptyEntity()
 * @method \App\Model\Entity\Lote newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Lote> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Lote get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Lote findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Lote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Lote> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Lote|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Lote saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Lote>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Lote>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Lote>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Lote> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Lote>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Lote>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Lote>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Lote> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LotesTable extends Table
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

        $this->setTable('lotes');
        $this->setDisplayField('numero_lote');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Fornecedors', [
            'foreignKey' => 'fornecedor_id',
            'className' => 'Fornecedores',
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('MovimentacoesEstoque', [
            'foreignKey' => 'lote_id',
        ]);
        $this->hasMany('VendasItens', [
            'foreignKey' => 'lote_id',
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
            ->notEmptyString('medicamento_id');

        $validator
            ->allowEmptyString('fornecedor_id');

        $validator
            ->scalar('numero_lote')
            ->maxLength('numero_lote', 80)
            ->requirePresence('numero_lote', 'create')
            ->notEmptyString('numero_lote');

        $validator
            ->date('data_fabricacao')
            ->allowEmptyDate('data_fabricacao');

        $validator
            ->date('data_validade')
            ->requirePresence('data_validade', 'create')
            ->notEmptyDate('data_validade');

        $validator
            ->decimal('quantidade_recebida')
            ->requirePresence('quantidade_recebida', 'create')
            ->notEmptyString('quantidade_recebida');

        $validator
            ->decimal('quantidade_disponivel')
            ->requirePresence('quantidade_disponivel', 'create')
            ->notEmptyString('quantidade_disponivel');

        $validator
            ->decimal('preco_custo')
            ->requirePresence('preco_custo', 'create')
            ->notEmptyString('preco_custo');

        $validator
            ->scalar('nota_fiscal')
            ->maxLength('nota_fiscal', 80)
            ->allowEmptyString('nota_fiscal');

        $validator
            ->dateTime('data_recebimento')
            ->requirePresence('data_recebimento', 'create')
            ->notEmptyDateTime('data_recebimento');

        $validator
            ->notEmptyString('usuario_id');

        $validator
            ->boolean('bloqueado')
            ->notEmptyString('bloqueado');

        $validator
            ->boolean('recolhido')
            ->notEmptyString('recolhido');

        $validator
            ->scalar('motivo_bloqueio')
            ->allowEmptyString('motivo_bloqueio');

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
        $rules->add($rules->isUnique(['medicamento_id', 'numero_lote']), ['errorField' => 'medicamento_id', 'message' => __('This combination of medicamento_id and numero_lote already exists')]);
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);
        $rules->add($rules->existsIn(['fornecedor_id'], 'Fornecedors'), ['errorField' => 'fornecedor_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
