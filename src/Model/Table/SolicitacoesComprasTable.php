<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SolicitacoesCompras Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Solicitantes
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Aprovadors
 * @property \App\Model\Table\FornecedoresTable&\Cake\ORM\Association\BelongsTo $FornecedorEscolhidos
 * @method \App\Model\Entity\SolicitacoesCompra newEmptyEntity()
 * @method \App\Model\Entity\SolicitacoesCompra newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SolicitacoesCompra> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SolicitacoesCompra get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SolicitacoesCompra findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SolicitacoesCompra patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SolicitacoesCompra> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SolicitacoesCompra|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SolicitacoesCompra saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesCompra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesCompra>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesCompra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesCompra> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesCompra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesCompra>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SolicitacoesCompra>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SolicitacoesCompra> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SolicitacoesComprasTable extends Table
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

        $this->setTable('solicitacoes_compras');
        $this->setDisplayField('numero');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Solicitantes', [
            'foreignKey' => 'solicitante_id',
            'className' => 'Usuarios',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Aprovadores', [
            'foreignKey' => 'aprovador_id',
            'className' => 'Usuarios',
        ]);
        $this->belongsTo('FornecedoresEscolhidos', [
            'foreignKey' => 'fornecedor_escolhido_id',
            'className' => 'Fornecedores',
        ]);
        $this->hasMany('SolicitacoesComprasItens', [
            'foreignKey' => 'solicitacao_compra_id',
        ]);
        $this->hasMany('Cotacoes', [
            'foreignKey' => 'solicitacao_compra_id',
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
            ->scalar('numero')
            ->maxLength('numero', 40)
            ->requirePresence('numero', 'create')
            ->notEmptyString('numero')
            ->add('numero', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->notEmptyString('solicitante_id');

        $validator
            ->allowEmptyString('aprovador_id');

        $validator
            ->allowEmptyString('fornecedor_escolhido_id');

        $validator
            ->scalar('status')
            ->maxLength('status', 40)
            ->notEmptyString('status');

        $validator
            ->scalar('justificativa')
            ->requirePresence('justificativa', 'create')
            ->notEmptyString('justificativa');

        $validator
            ->decimal('valor_estimado')
            ->notEmptyString('valor_estimado');

        $validator
            ->decimal('valor_aprovado')
            ->allowEmptyString('valor_aprovado');

        $validator
            ->dateTime('solicitada_em')
            ->allowEmptyDateTime('solicitada_em');

        $validator
            ->dateTime('aprovada_em')
            ->allowEmptyDateTime('aprovada_em');

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
        $rules->add($rules->isUnique(['numero']), ['errorField' => 'numero']);
        $rules->add($rules->existsIn(['solicitante_id'], 'Solicitantes'), ['errorField' => 'solicitante_id']);
        $rules->add($rules->existsIn(['aprovador_id'], 'Aprovadores'), ['errorField' => 'aprovador_id']);
        $rules->add($rules->existsIn(['fornecedor_escolhido_id'], 'FornecedoresEscolhidos'), ['errorField' => 'fornecedor_escolhido_id']);

        return $rules;
    }
}
