<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Validation\DocumentoValidator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fornecedores Model
 *
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\BelongsToMany $Produtos
 * @method \App\Model\Entity\Fornecedore newEmptyEntity()
 * @method \App\Model\Entity\Fornecedore newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Fornecedore> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fornecedore get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Fornecedore findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Fornecedore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Fornecedore> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fornecedore|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Fornecedore saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedore>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedore> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedore>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fornecedore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fornecedore> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FornecedoresTable extends Table
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

        $this->setTable('fornecedores');
        $this->setDisplayField('razao_social');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Produtos', [
            'foreignKey' => 'fornecedore_id',
            'targetForeignKey' => 'produto_id',
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
            ->scalar('razao_social')
            ->maxLength('razao_social', 180)
            ->requirePresence('razao_social', 'create')
            ->notEmptyString('razao_social');

        $validator
            ->scalar('nome_fantasia')
            ->maxLength('nome_fantasia', 180)
            ->allowEmptyString('nome_fantasia');

        $validator
            ->scalar('cnpj')
            ->maxLength('cnpj', 14)
            ->requirePresence('cnpj', 'create')
            ->notEmptyString('cnpj')
            ->add('cnpj', 'validCnpj', [
                'rule' => [DocumentoValidator::class, 'cnpj'],
                'message' => 'Informe um CNPJ válido.',
            ])
            ->add('cnpj', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('inscricao_estadual')
            ->maxLength('inscricao_estadual', 30)
            ->allowEmptyString('inscricao_estadual');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 30)
            ->allowEmptyString('telefone');

        $validator
            ->scalar('endereco')
            ->allowEmptyString('endereco');

        $validator
            ->scalar('contato_nome')
            ->maxLength('contato_nome', 150)
            ->allowEmptyString('contato_nome');

        $validator
            ->integer('prazo_medio_entrega')
            ->notEmptyString('prazo_medio_entrega');

        $validator
            ->scalar('condicoes_pagamento')
            ->allowEmptyString('condicoes_pagamento');

        $validator
            ->decimal('avaliacao')
            ->allowEmptyString('avaliacao');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

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
        $rules->add($rules->isUnique(['cnpj']), ['errorField' => 'cnpj']);

        return $rules;
    }
}
