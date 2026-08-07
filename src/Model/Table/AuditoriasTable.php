<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Auditorias Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @method \App\Model\Entity\Auditoria newEmptyEntity()
 * @method \App\Model\Entity\Auditoria newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Auditoria> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Auditoria get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Auditoria findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Auditoria patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Auditoria> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Auditoria|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Auditoria saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Auditoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Auditoria>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Auditoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Auditoria> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Auditoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Auditoria>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Auditoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Auditoria> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AuditoriasTable extends Table
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

        $this->setTable('auditorias');
        $this->setDisplayField('acao');
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
            ->scalar('acao')
            ->maxLength('acao', 80)
            ->requirePresence('acao', 'create')
            ->notEmptyString('acao');

        $validator
            ->scalar('modulo')
            ->maxLength('modulo', 80)
            ->requirePresence('modulo', 'create')
            ->notEmptyString('modulo');

        $validator
            ->allowEmptyString('registro_id');

        $validator
            ->allowEmptyString('dados_anteriores');

        $validator
            ->allowEmptyString('dados_posteriores');

        $validator
            ->ip('endereco_ip')
            ->allowEmptyString('endereco_ip');

        $validator
            ->scalar('user_agent')
            ->allowEmptyString('user_agent');

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
