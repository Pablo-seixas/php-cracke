<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use DateTimeImmutable;

/**
 * Usuarios Model
 *
 * @property \App\Model\Table\ProfissionaisTable&\Cake\ORM\Association\HasOne $Profissionais
 * @property \App\Model\Table\AuditoriasTable&\Cake\ORM\Association\HasMany $Auditorias
 * @property \App\Model\Table\ComprasTable&\Cake\ORM\Association\HasMany $Compras
 * @property \App\Model\Table\LotesTable&\Cake\ORM\Association\HasMany $Lotes
 * @property \App\Model\Table\MovimentacoesCaixaTable&\Cake\ORM\Association\HasMany $MovimentacoesCaixa
 * @property \App\Model\Table\MovimentacoesEstoqueTable&\Cake\ORM\Association\HasMany $MovimentacoesEstoque
 * @property \App\Model\Table\NotificacoesTable&\Cake\ORM\Association\HasMany $Notificacoes
 * @property \App\Model\Table\ProntuariosHistoricosTable&\Cake\ORM\Association\HasMany $ProntuariosHistoricos
 * @property \App\Model\Table\TentativasLoginTable&\Cake\ORM\Association\HasMany $TentativasLogin
 * @property \App\Model\Table\VendasTable&\Cake\ORM\Association\HasMany $Vendas
 * @property \App\Model\Table\PerfisTable&\Cake\ORM\Association\BelongsToMany $Perfis
 * @method \App\Model\Entity\Usuario newEmptyEntity()
 * @method \App\Model\Entity\Usuario newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Usuario> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Usuario get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Usuario findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Usuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Usuario> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Usuario|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Usuario saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Usuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Usuario>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Usuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Usuario> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Usuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Usuario>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Usuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Usuario> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsuariosTable extends Table
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

        $this->setTable('usuarios');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('Profissionais', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('Auditorias', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('Compras', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('Lotes', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('MovimentacoesCaixa', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('MovimentacoesEstoque', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('Notificacoes', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('ProntuariosHistoricos', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('TentativasLogin', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->hasMany('Vendas', [
            'foreignKey' => 'usuario_id',
        ]);
        $this->belongsToMany('Perfis', [
            'foreignKey' => 'usuario_id',
            'targetForeignKey' => 'perfil_id',
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
            ->maxLength('nome', 150)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('senha')
            ->maxLength('senha', 255)
            ->minLength('senha', 12, 'A senha deve possuir ao menos 12 caracteres.')
            ->requirePresence('senha', 'create')
            ->notEmptyString('senha');

        $validator
            ->boolean('ativo')
            ->notEmptyString('ativo');

        $validator
            ->dateTime('bloqueado_ate')
            ->allowEmptyDateTime('bloqueado_ate');

        $validator
            ->dateTime('ultimo_login')
            ->allowEmptyDateTime('ultimo_login');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        return $validator;
    }

    /**
     * Finder used by Authentication. Inactive, deleted and locked users are excluded.
     *
     * @param \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Usuario> $query Query instance.
     * @param array<string, mixed> $options Finder options.
     * @return \Cake\ORM\Query\SelectQuery<\App\Model\Entity\Usuario>
     */
    public function findAutenticacao(SelectQuery $query, array $options): SelectQuery
    {
        return $query
            ->contain(['Perfis.Permissoes'])
            ->where([
                'Usuarios.ativo' => true,
                'Usuarios.deleted IS' => null,
                'OR' => [
                    'Usuarios.bloqueado_ate IS' => null,
                    'Usuarios.bloqueado_ate <=' => new DateTimeImmutable(),
                ],
            ]);
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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
