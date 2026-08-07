<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Configuracoes Model
 *
 * @method \App\Model\Entity\Configuraco newEmptyEntity()
 * @method \App\Model\Entity\Configuraco newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Configuraco> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Configuraco get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Configuraco findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Configuraco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Configuraco> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Configuraco|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Configuraco saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Configuraco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Configuraco>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Configuraco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Configuraco> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Configuraco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Configuraco>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Configuraco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Configuraco> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConfiguracoesTable extends Table
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

        $this->setTable('configuracoes');
        $this->setDisplayField('chave');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('chave')
            ->maxLength('chave', 120)
            ->requirePresence('chave', 'create')
            ->notEmptyString('chave')
            ->add('chave', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->notEmptyString('valor');

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->allowEmptyString('modified_by');

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
        $rules->add($rules->isUnique(['chave']), ['errorField' => 'chave']);

        return $rules;
    }
}
