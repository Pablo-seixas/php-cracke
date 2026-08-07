<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Fabricantes Model
 *
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\HasMany $Medicamentos
 * @property \App\Model\Table\ProdutosTable&\Cake\ORM\Association\HasMany $Produtos
 * @method \App\Model\Entity\Fabricante newEmptyEntity()
 * @method \App\Model\Entity\Fabricante newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Fabricante> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fabricante get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Fabricante findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Fabricante patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Fabricante> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fabricante|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Fabricante saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Fabricante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fabricante>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fabricante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fabricante> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fabricante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fabricante>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fabricante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fabricante> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FabricantesTable extends Table
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

        $this->setTable('fabricantes');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Medicamentos', [
            'foreignKey' => 'fabricante_id',
        ]);
        $this->hasMany('Produtos', [
            'foreignKey' => 'fabricante_id',
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
            ->scalar('nome')
            ->maxLength('nome', 150)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome')
            ->add('nome', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('cnpj')
            ->maxLength('cnpj', 14)
            ->allowEmptyString('cnpj')
            ->add('cnpj', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['nome']), ['errorField' => 'nome']);
        $rules->add($rules->isUnique(['cnpj'], ['allowMultipleNulls' => true]), ['errorField' => 'cnpj']);

        return $rules;
    }
}
