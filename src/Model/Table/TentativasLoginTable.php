<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TentativasLogin Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @method \App\Model\Entity\TentativasLogin newEmptyEntity()
 * @method \App\Model\Entity\TentativasLogin newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TentativasLogin> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TentativasLogin get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TentativasLogin findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TentativasLogin patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TentativasLogin> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TentativasLogin|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TentativasLogin saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TentativasLogin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TentativasLogin>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TentativasLogin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TentativasLogin> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TentativasLogin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TentativasLogin>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TentativasLogin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TentativasLogin> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TentativasLoginTable extends Table
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

        $this->setTable('tentativas_login');
        $this->setDisplayField('identificador');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
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
            ->allowEmptyString('usuario_id');

        $validator
            ->scalar('identificador')
            ->maxLength('identificador', 190)
            ->requirePresence('identificador', 'create')
            ->notEmptyString('identificador');

        $validator
            ->boolean('sucesso')
            ->notEmptyString('sucesso');

        $validator
            ->ip('endereco_ip')
            ->allowEmptyString('endereco_ip');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('motivo')
            ->maxLength('motivo', 120)
            ->allowEmptyString('motivo');

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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
