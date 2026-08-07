<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Perfis Model
 *
 * @property \App\Model\Table\PermissoesTable&\Cake\ORM\Association\BelongsToMany $Permissoes
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsToMany $Usuarios
 * @method \App\Model\Entity\Perfi newEmptyEntity()
 * @method \App\Model\Entity\Perfi newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Perfi> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Perfi get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Perfi findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Perfi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Perfi> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Perfi|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Perfi saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Perfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Perfi>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Perfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Perfi> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Perfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Perfi>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Perfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Perfi> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PerfisTable extends Table
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

        $this->setTable('perfis');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Permissoes', [
            'foreignKey' => 'perfil_id',
            'targetForeignKey' => 'permissao_id',
            'joinTable' => 'perfis_permissoes',
        ]);
        $this->belongsToMany('Usuarios', [
            'foreignKey' => 'perfil_id',
            'targetForeignKey' => 'usuario_id',
            'joinTable' => 'usuarios_perfis',
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
            ->maxLength('nome', 80)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome')
            ->add('nome', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->boolean('ativo')
            ->notEmptyString('ativo');

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

        return $rules;
    }
}
