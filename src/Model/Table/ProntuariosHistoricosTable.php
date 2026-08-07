<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProntuariosHistoricos Model
 *
 * @property \App\Model\Table\ProntuariosTable&\Cake\ORM\Association\BelongsTo $Prontuarios
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @method \App\Model\Entity\ProntuariosHistorico newEmptyEntity()
 * @method \App\Model\Entity\ProntuariosHistorico newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ProntuariosHistorico> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProntuariosHistorico get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ProntuariosHistorico findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ProntuariosHistorico patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ProntuariosHistorico> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProntuariosHistorico|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ProntuariosHistorico saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ProntuariosHistorico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProntuariosHistorico>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProntuariosHistorico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProntuariosHistorico> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProntuariosHistorico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProntuariosHistorico>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProntuariosHistorico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProntuariosHistorico> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProntuariosHistoricosTable extends Table
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

        $this->setTable('prontuarios_historicos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Prontuarios', [
            'foreignKey' => 'prontuario_id',
            'joinType' => 'INNER',
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
            ->notEmptyString('prontuario_id');

        $validator
            ->integer('versao')
            ->requirePresence('versao', 'create')
            ->notEmptyString('versao');

        $validator
            ->requirePresence('dados', 'create')
            ->notEmptyString('dados');

        $validator
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('motivo')
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
        $rules->add($rules->isUnique(['prontuario_id', 'versao']), ['errorField' => 'prontuario_id', 'message' => __('This combination of prontuario_id and versao already exists')]);
        $rules->add($rules->existsIn(['prontuario_id'], 'Prontuarios'), ['errorField' => 'prontuario_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
