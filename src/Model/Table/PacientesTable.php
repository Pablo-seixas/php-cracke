<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Validation\DocumentoValidator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pacientes Model
 *
 * @property \App\Model\Table\ClientesTable&\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\ProntuariosTable&\Cake\ORM\Association\HasOne $Prontuarios
 * @property \App\Model\Table\AtendimentosTable&\Cake\ORM\Association\HasMany $Atendimentos
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\HasMany $Consultas
 * @property \App\Model\Table\ContasReceberTable&\Cake\ORM\Association\HasMany $ContasReceber
 * @property \App\Model\Table\ReceitasTable&\Cake\ORM\Association\HasMany $Receitas
 * @method \App\Model\Entity\Paciente newEmptyEntity()
 * @method \App\Model\Entity\Paciente newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Paciente> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Paciente get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Paciente findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Paciente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Paciente> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Paciente|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Paciente saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PacientesTable extends Table
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

        $this->setTable('pacientes');
        $this->setDisplayField('nome_completo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'cliente_id',
        ]);
        $this->hasOne('Prontuarios', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->hasMany('Atendimentos', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->hasMany('Consultas', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->hasMany('ContasReceber', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->hasMany('Receitas', [
            'foreignKey' => 'paciente_id',
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
            ->allowEmptyString('cliente_id')
            ->add('cliente_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('nome_completo')
            ->maxLength('nome_completo', 180)
            ->requirePresence('nome_completo', 'create')
            ->notEmptyString('nome_completo');

        $validator
            ->scalar('nome_social')
            ->maxLength('nome_social', 180)
            ->allowEmptyString('nome_social');

        $validator
            ->scalar('cpf')
            ->maxLength('cpf', 11)
            ->allowEmptyString('cpf')
            ->add('cpf', 'validCpf', [
                'rule' => fn($value): bool => $value === null || $value === '' || DocumentoValidator::cpf($value),
                'message' => 'Informe um CPF válido.',
            ])
            ->add('cpf', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->date('data_nascimento')
            ->requirePresence('data_nascimento', 'create')
            ->notEmptyDate('data_nascimento');

        $validator
            ->scalar('sexo')
            ->maxLength('sexo', 30)
            ->allowEmptyString('sexo');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 30)
            ->allowEmptyString('telefone');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('endereco')
            ->allowEmptyString('endereco');

        $validator
            ->scalar('responsavel')
            ->maxLength('responsavel', 180)
            ->allowEmptyString('responsavel');

        $validator
            ->scalar('contato_emergencia')
            ->maxLength('contato_emergencia', 180)
            ->allowEmptyString('contato_emergencia');

        $validator
            ->scalar('alergias')
            ->allowEmptyString('alergias');

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
        $rules->add($rules->isUnique(['cliente_id'], ['allowMultipleNulls' => true]), ['errorField' => 'cliente_id']);
        $rules->add($rules->isUnique(['cpf'], ['allowMultipleNulls' => true]), ['errorField' => 'cpf']);
        $rules->add($rules->existsIn(['cliente_id'], 'Clientes'), ['errorField' => 'cliente_id']);

        return $rules;
    }
}
