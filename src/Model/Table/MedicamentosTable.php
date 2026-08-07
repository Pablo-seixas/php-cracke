<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Medicamentos Model
 *
 * @property \App\Model\Table\CategoriasTable&\Cake\ORM\Association\BelongsTo $Categorias
 * @property \App\Model\Table\FabricantesTable&\Cake\ORM\Association\BelongsTo $Fabricantes
 * @property \App\Model\Table\ComprasItensTable&\Cake\ORM\Association\HasMany $ComprasItens
 * @property \App\Model\Table\FornecedoresProdutosTable&\Cake\ORM\Association\HasMany $FornecedoresProdutos
 * @property \App\Model\Table\LotesTable&\Cake\ORM\Association\HasMany $Lotes
 * @property \App\Model\Table\MovimentacoesEstoqueTable&\Cake\ORM\Association\HasMany $MovimentacoesEstoque
 * @property \App\Model\Table\SolicitacoesComprasItensTable&\Cake\ORM\Association\HasMany $SolicitacoesComprasItens
 * @property \App\Model\Table\VendasItensTable&\Cake\ORM\Association\HasMany $VendasItens
 * @method \App\Model\Entity\Medicamento newEmptyEntity()
 * @method \App\Model\Entity\Medicamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Medicamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Medicamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Medicamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Medicamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Medicamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Medicamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Medicamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MedicamentosTable extends Table
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

        $this->setTable('medicamentos');
        $this->setDisplayField('codigo_interno');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Categorias', [
            'foreignKey' => 'categoria_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Fabricantes', [
            'foreignKey' => 'fabricante_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ComprasItens', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->hasMany('FornecedoresProdutos', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->hasMany('Lotes', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->hasMany('MovimentacoesEstoque', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->hasMany('SolicitacoesComprasItens', [
            'foreignKey' => 'medicamento_id',
        ]);
        $this->hasMany('VendasItens', [
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
            ->notEmptyString('categoria_id');

        $validator
            ->notEmptyString('fabricante_id');

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
            ->scalar('nome_comercial')
            ->maxLength('nome_comercial', 180)
            ->requirePresence('nome_comercial', 'create')
            ->notEmptyString('nome_comercial');

        $validator
            ->scalar('principio_ativo')
            ->maxLength('principio_ativo', 180)
            ->requirePresence('principio_ativo', 'create')
            ->notEmptyString('principio_ativo');

        $validator
            ->scalar('forma_farmaceutica')
            ->maxLength('forma_farmaceutica', 100)
            ->allowEmptyString('forma_farmaceutica');

        $validator
            ->scalar('concentracao')
            ->maxLength('concentracao', 100)
            ->allowEmptyString('concentracao');

        $validator
            ->scalar('apresentacao')
            ->maxLength('apresentacao', 150)
            ->allowEmptyString('apresentacao');

        $validator
            ->scalar('unidade_medida')
            ->maxLength('unidade_medida', 30)
            ->requirePresence('unidade_medida', 'create')
            ->notEmptyString('unidade_medida');

        $validator
            ->scalar('registro_sanitario')
            ->maxLength('registro_sanitario', 80)
            ->allowEmptyString('registro_sanitario')
            ->add('registro_sanitario', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
            ->scalar('localizacao')
            ->maxLength('localizacao', 100)
            ->allowEmptyString('localizacao');

        $validator
            ->boolean('necessita_receita')
            ->notEmptyString('necessita_receita');

        $validator
            ->scalar('tipo_receita')
            ->maxLength('tipo_receita', 50)
            ->allowEmptyString('tipo_receita');

        $validator
            ->boolean('controlado')
            ->notEmptyString('controlado');

        $validator
            ->boolean('refrigerado')
            ->notEmptyString('refrigerado');

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
        $rules->add($rules->isUnique(['registro_sanitario'], ['allowMultipleNulls' => true]), ['errorField' => 'registro_sanitario']);
        $rules->add($rules->existsIn(['categoria_id'], 'Categorias'), ['errorField' => 'categoria_id']);
        $rules->add($rules->existsIn(['fabricante_id'], 'Fabricantes'), ['errorField' => 'fabricante_id']);

        return $rules;
    }
}
