<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;

/**
 * UsuariosPerfis Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\PerfisTable&\Cake\ORM\Association\BelongsTo $Perfils
 * @method \App\Model\Entity\UsuariosPerfi newEmptyEntity()
 * @method \App\Model\Entity\UsuariosPerfi newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuariosPerfi> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosPerfi get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UsuariosPerfi findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UsuariosPerfi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuariosPerfi> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosPerfi|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UsuariosPerfi saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPerfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPerfi>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPerfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPerfi> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPerfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPerfi>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPerfi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPerfi> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsuariosPerfisTable extends Table
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

        $this->setTable('usuarios_perfis');
        $this->setDisplayField(['usuario_id', 'perfil_id']);
        $this->setPrimaryKey(['usuario_id', 'perfil_id']);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Perfis', [
            'foreignKey' => 'perfil_id',
            'className' => 'Perfis',
            'joinType' => 'INNER',
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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['perfil_id'], 'Perfis'), ['errorField' => 'perfil_id']);

        return $rules;
    }
}
