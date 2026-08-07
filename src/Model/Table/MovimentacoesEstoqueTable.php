<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MovimentacoesEstoque Model
 *
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsTo $Produtos
 * @property \App\Model\Table\LotesTable&\Cake\ORM\Association\BelongsTo $Lotes
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @method \App\Model\Entity\MovimentacoesEstoque newEmptyEntity()
 * @method \App\Model\Entity\MovimentacoesEstoque newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MovimentacoesEstoque> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MovimentacoesEstoque get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MovimentacoesEstoque findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MovimentacoesEstoque patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MovimentacoesEstoque> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MovimentacoesEstoque|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MovimentacoesEstoque saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesEstoque>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesEstoque>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesEstoque>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesEstoque> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesEstoque>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesEstoque>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesEstoque>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesEstoque> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MovimentacoesEstoqueTable extends Table
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

        $this->setTable('movimentacoes_estoque');
        $this->setDisplayField('tipo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->belongsTo('Produtos', [
            'foreignKey' => 'produto_id',
        ]);
        $this->belongsTo('Lotes', [
            'foreignKey' => 'lote_id',
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
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
            ->allowEmptyString('medicamento_id');

        $validator
            ->allowEmptyString('produto_id');

        $validator
            ->allowEmptyString('lote_id');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 30)
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->decimal('quantidade_anterior')
            ->requirePresence('quantidade_anterior', 'create')
            ->notEmptyString('quantidade_anterior');

        $validator
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmptyString('quantidade');

        $validator
            ->decimal('quantidade_posterior')
            ->requirePresence('quantidade_posterior', 'create')
            ->notEmptyString('quantidade_posterior');

        $validator
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('motivo')
            ->requirePresence('motivo', 'create')
            ->notEmptyString('motivo');

        $validator
            ->scalar('documento_tipo')
            ->maxLength('documento_tipo', 50)
            ->allowEmptyString('documento_tipo');

        $validator
            ->allowEmptyString('documento_id');

        $validator
            ->ip('endereco_ip')
            ->allowEmptyString('endereco_ip');

        $validator
            ->scalar('observacao')
            ->allowEmptyString('observacao');

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
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id']);
        $rules->add($rules->existsIn(['produto_id'], 'Produtos'), ['errorField' => 'produto_id']);
        $rules->add($rules->existsIn(['lote_id'], 'Lotes'), ['errorField' => 'lote_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
