<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Caixas Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $UsuarioAberturas
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $UsuarioFechamentos
 * @property \App\Model\Table\MovimentacoesCaixaTable&\Cake\ORM\Association\HasMany $MovimentacoesCaixa
 * @property \App\Model\Table\PagamentosTable&\Cake\ORM\Association\HasMany $Pagamentos
 * @method \App\Model\Entity\Caixa newEmptyEntity()
 * @method \App\Model\Entity\Caixa newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Caixa> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Caixa get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Caixa findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Caixa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Caixa> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Caixa|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Caixa saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Caixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Caixa>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Caixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Caixa> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Caixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Caixa>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Caixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Caixa> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CaixasTable extends Table
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

        $this->setTable('caixas');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('UsuarioAberturas', [
            'foreignKey' => 'usuario_abertura_id',
            'className' => 'Usuarios',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('UsuarioFechamentos', [
            'foreignKey' => 'usuario_fechamento_id',
            'className' => 'Usuarios',
        ]);
        $this->hasMany('MovimentacoesCaixa', [
            'foreignKey' => 'caixa_id',
        ]);
        $this->hasMany('Pagamentos', [
            'foreignKey' => 'caixa_id',
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
            ->notEmptyString('usuario_abertura_id');

        $validator
            ->allowEmptyString('usuario_fechamento_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

        $validator
            ->decimal('saldo_inicial')
            ->notEmptyString('saldo_inicial');

        $validator
            ->decimal('saldo_final')
            ->allowEmptyString('saldo_final');

        $validator
            ->dateTime('aberto_em')
            ->notEmptyDateTime('aberto_em');

        $validator
            ->dateTime('fechado_em')
            ->allowEmptyDateTime('fechado_em');

        $validator
            ->scalar('observacoes')
            ->allowEmptyString('observacoes');

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
        $rules->add($rules->existsIn(['usuario_abertura_id'], 'UsuarioAberturas'), ['errorField' => 'usuario_abertura_id']);
        $rules->add($rules->existsIn(['usuario_fechamento_id'], 'UsuarioFechamentos'), ['errorField' => 'usuario_fechamento_id']);

        return $rules;
    }
}
