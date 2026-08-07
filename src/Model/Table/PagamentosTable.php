<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pagamentos Model
 *
 * @property \App\Model\Table\VendasTable&\Cake\ORM\Association\BelongsTo $Vendas
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 * @property \App\Model\Table\CaixasTable&\Cake\ORM\Association\BelongsTo $Caixas
 * @property \App\Model\Table\MovimentacoesCaixaTable&\Cake\ORM\Association\HasMany $MovimentacoesCaixa
 * @method \App\Model\Entity\Pagamento newEmptyEntity()
 * @method \App\Model\Entity\Pagamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Pagamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Pagamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Pagamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Pagamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pagamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Pagamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Pagamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Pagamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Pagamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Pagamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Pagamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Pagamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Pagamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Pagamento> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagamentosTable extends Table
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

        $this->setTable('pagamentos');
        $this->setDisplayField('forma');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Vendas', [
            'foreignKey' => 'venda_id',
        ]);
        $this->belongsTo('Consultas', [
            'foreignKey' => 'consulta_id',
        ]);
        $this->belongsTo('Caixas', [
            'foreignKey' => 'caixa_id',
        ]);
        $this->hasMany('MovimentacoesCaixa', [
            'foreignKey' => 'pagamento_id',
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
            ->allowEmptyString('venda_id');

        $validator
            ->allowEmptyString('consulta_id');

        $validator
            ->allowEmptyString('caixa_id');

        $validator
            ->scalar('forma')
            ->maxLength('forma', 30)
            ->requirePresence('forma', 'create')
            ->notEmptyString('forma');

        $validator
            ->decimal('valor')
            ->requirePresence('valor', 'create')
            ->notEmptyString('valor');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

        $validator
            ->dateTime('pago_em')
            ->allowEmptyDateTime('pago_em');

        $validator
            ->dateTime('estornado_em')
            ->allowEmptyDateTime('estornado_em');

        $validator
            ->scalar('referencia')
            ->maxLength('referencia', 120)
            ->allowEmptyString('referencia');

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
        $rules->add($rules->existsIn(['venda_id'], 'Vendas'), ['errorField' => 'venda_id']);
        $rules->add($rules->existsIn(['consulta_id'], 'Consultas'), ['errorField' => 'consulta_id']);
        $rules->add($rules->existsIn(['caixa_id'], 'Caixas'), ['errorField' => 'caixa_id']);

        return $rules;
    }
}
