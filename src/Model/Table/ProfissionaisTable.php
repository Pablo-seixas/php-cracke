<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Profissionais Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\EspecialidadesTable&\Cake\ORM\Association\BelongsTo $Especialidades
 * @method \App\Model\Entity\Profissionai newEmptyEntity()
 * @method \App\Model\Entity\Profissionai newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Profissionai> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Profissionai get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Profissionai findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Profissionai patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Profissionai> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Profissionai|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Profissionai saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Profissionai>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Profissionai>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Profissionai>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Profissionai> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Profissionai>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Profissionai>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Profissionai>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Profissionai> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfissionaisTable extends Table
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

        $this->setTable('profissionais');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->belongsTo('Especialidades', [
            'foreignKey' => 'especialidade_id',
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
            ->allowEmptyString('usuario_id')
            ->add('usuario_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmptyString('especialidade_id');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 180)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 11)
            ->requirePresence('cpf', 'create')
            ->notEmptyString('cpf')
            ->add('cpf', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 30)
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->scalar('registro_profissional')
            ->maxLength('registro_profissional', 60)
            ->allowEmptyString('registro_profissional');

        $validator
            ->scalar('conselho')
            ->maxLength('conselho', 30)
            ->allowEmptyString('conselho');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 30)
            ->allowEmptyString('telefone');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->notEmptyString('dias_atendimento');

        $validator
            ->notEmptyString('horarios');

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
        $rules->add($rules->isUnique(['usuario_id'], ['allowMultipleNulls' => true]), ['errorField' => 'usuario_id']);
        $rules->add($rules->isUnique(['cpf']), ['errorField' => 'cpf']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['especialidade_id'], 'Especialidades'), ['errorField' => 'especialidade_id']);

        return $rules;
    }
}
