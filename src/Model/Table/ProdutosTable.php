<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Produtos Model
 *
 * @property \App\Model\Table\CategoriasTable&\Cake\ORM\Association\BelongsTo $Categorias
 * @property \App\Model\Table\FabricantesTable&\Cake\ORM\Association\BelongsTo $Fabricantes
 * @property \App\Model\Table\ComprasItensTable&\Cake\ORM\Association\HasMany $ComprasItens
 * @property \App\Model\Table\MovimentacoesEstoqueTable&\Cake\ORM\Association\HasMany $MovimentacoesEstoque
 * @property \App\Model\Table\SolicitacoesComprasItensTable&\Cake\ORM\Association\HasMany $SolicitacoesComprasItens
 * @property \App\Model\Table\VendasItensTable&\Cake\ORM\Association\HasMany $VendasItens
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsToMany $Fornecedores
 * @method \App\Model\Entity\Produto newEmptyEntity()
 * @method \App\Model\Entity\Produto newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Produto> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Produto get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Produto findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Produto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Produto> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Produto|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Produto saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Produto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Produto> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProdutosTable extends Table
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

        $this->setTable('produtos');
        $this->setDisplayField('codigo_interno');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Categorias', [
            'foreignKey' => 'categoria_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Fabricantes', [
            'foreignKey' => 'fabricante_id',
        ]);
        $this->hasMany('ComprasItens', [
            'foreignKey' => 'produto_id',
        ]);
        $this->hasMany('MovimentacoesEstoque', [
            'foreignKey' => 'produto_id',
        ]);
        $this->hasMany('SolicitacoesComprasItens', [
            'foreignKey' => 'produto_id',
        ]);
        $this->hasMany('VendasItens', [
            'foreignKey' => 'produto_id',
        ]);
        $this->belongsToMany('Fornecedores', [
            'foreignKey' => 'produto_id',
            'targetForeignKey' => 'fornecedor_id',
            'joinTable' => 'fornecedores_produtos',
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
            ->notEmptyString('categoria_id');

        $validator
            ->allowEmptyString('fabricante_id');

        $validator
            ->scalar('codigo_interno')
            ->maxLength('codigo_interno', 50)
            ->requirePresence('codigo_interno', 'create')
            ->notEmptyString('codigo_interno')
            ->add('codigo_interno', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('codigo_barras')
            ->maxLength('codigo_barras', 30)
            ->allowEmptyString('codigo_barras')
            ->add('codigo_barras', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('nome')
            ->maxLength('nome', 180)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('unidade_medida')
            ->maxLength('unidade_medida', 30)
            ->requirePresence('unidade_medida', 'create')
            ->notEmptyString('unidade_medida');

        $validator
            ->decimal('preco_custo')
            ->notEmptyString('preco_custo');

        $validator
            ->decimal('preco_venda')
            ->notEmptyString('preco_venda');

        $validator
            ->decimal('estoque_minimo')
            ->notEmptyString('estoque_minimo');

        $validator
            ->decimal('estoque_maximo')
            ->notEmptyString('estoque_maximo');

        $validator
            ->boolean('ativo')
            ->notEmptyString('ativo');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

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
        $rules->add($rules->isUnique(['codigo_interno']), ['errorField' => 'codigo_interno']);
        $rules->add($rules->isUnique(['codigo_barras'], ['allowMultipleNulls' => true]), ['errorField' => 'codigo_barras']);
        $rules->add($rules->existsIn(['categoria_id'], 'Categorias'), ['errorField' => 'categoria_id']);
        $rules->add($rules->existsIn(['fabricante_id'], 'Fabricantes'), ['errorField' => 'fabricante_id']);

        return $rules;
    }
}
