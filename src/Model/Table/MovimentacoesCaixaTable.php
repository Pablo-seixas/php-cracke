<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MovimentacoesCaixa Model
 *
 * @property \App\Model\Table\CaixasTable&\Cake\ORM\Association\BelongsTo $Caixas
 * @property \App\Model\Table\PagamentosTable&\Cake\ORM\Association\BelongsTo $Pagamentos
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @method \App\Model\Entity\MovimentacoesCaixa newEmptyEntity()
 * @method \App\Model\Entity\MovimentacoesCaixa newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MovimentacoesCaixa> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MovimentacoesCaixa get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MovimentacoesCaixa findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MovimentacoesCaixa patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MovimentacoesCaixa> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MovimentacoesCaixa|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MovimentacoesCaixa saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesCaixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesCaixa>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesCaixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesCaixa> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesCaixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesCaixa>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MovimentacoesCaixa>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MovimentacoesCaixa> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MovimentacoesCaixaTable extends Table
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

        $this->setTable('movimentacoes_caixa');
        $this->setDisplayField('tipo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Caixas', [
            'foreignKey' => 'caixa_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Pagamentos', [
            'foreignKey' => 'pagamento_id',
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
            ->notEmptyString('caixa_id');

        $validator
            ->allowEmptyString('pagamento_id');

        $validator
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 20)
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

        $validator
            ->scalar('motivo')
            ->requirePresence('motivo', 'create')
            ->notEmptyString('motivo');

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
        $rules->add($rules->existsIn(['caixa_id'], 'Caixas'), ['errorField' => 'caixa_id']);
        $rules->add($rules->existsIn(['pagamento_id'], 'Pagamentos'), ['errorField' => 'pagamento_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
