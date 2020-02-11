<?php

use Illuminate\Database\Seeder;
use \TCG\Voyager\Models\Permission;
use \TCG\Voyager\Models\Role;

class GenetviRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Roles voyager
        $role = Role::firstOrNew(['name' => 'admin']);
        if (!$role->exists) {
            $role->fill([
                    'display_name' => __('voyager::seeders.roles.admin'),
                ])->save();
        }

        $role = Role::firstOrNew(['name' => 'user']);
        if (!$role->exists) {
            $role->fill([
                    'display_name' => __('voyager::seeders.roles.user'),
                ])->save();
        }
        
        //Permisos voyager
        $keys = [
            'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');

        Permission::generateFor('users');

        Permission::generateFor('instrumentos');

        Permission::generateFor('hooks');

        Permission::generateFor('settings');

        Permission::generateFor('categorias');

        Permission::generateFor('indicadores');

        Permission::generateFor('periodos_lectivos');

        Permission::generateFor('cursos_participantes_roles');

        Permission::generateFor('momentos_evaluacion');

        Permission::generateFor('invitaciones');
        
        
        //Creamos todos los Roles
        $list_dependencias=array(
            "facultad_de_arquitectura_y_urbanismo"          => "Facultad de Arquitectura y Urbanismo",
            "facultad_de_agronomia"                         => "Facultad de Agronomía",
            "facultad_de_ciencias"                          => "Facultad de Ciencias",
            "facultad_de_ciencias_juridicas_y_politicas"    => "Facultad de Ciencias Jurídicas y Políticas",
            "facultad_de_ciencias_economicas_y_sociales"    => "Facultad de Ciencias Económicas y Sociales",
            "facultad_de_farmacia"                          => "Facultad de Farmacia",
            "facultad_de_humanidades_y_educacion"           => "Facultad de Humanidades y Educación",
            "facultad_de_ingenieria"                        => "Facultad de Ingeniería",
            "facultad_de_medicina"                          => "Facultad de Medicina",
            "facultad_de_odontologia"                       => "Facultad de Odontología",
            "facultad_de_ciencias_veterinarias"             => "Facultad de Ciencias Veterinarias",
            "rectorado"                                     => "Rectorado",
            "vicerrectorado_academico"                      => "Vicerrectorado Académico",
            "vicerrectorado_administrativo"                 => "Vicerrectorado Administrativo",
            "secretaria"                                    => "Secretaría",
            "fundaciones_asociaciones"                      => "Fundaciones - Asociaciones",
            "otras_dependencias"                            => "Otras Dependencias",
        );
        
        foreach($list_dependencias as $name=>$display_name){
            $role = Role::firstOrNew(['name' => 'coordinador_'.$name]);
            if (!$role->exists) {
                $role->fill([
                        'display_name' => __('Coordinador '.$display_name),
                    ])->save();
            }
        }
        
        //Creamos los permisos para los roles anteriores
        $keys = [
                'ver_',
                'sincronizar_',
                'habilitar_evaluacion_',
            ];
        foreach($list_dependencias as $name=>$display_name){
            foreach ($keys as $key) {
                Permission::firstOrCreate([
                    'key'        => $key.$name,
                    'table_name' => $display_name,
                ]);
            }
        }
        
        //Asignamos los Permisos para el Rol
        foreach($list_dependencias as $name=>$display_name){
            $role = Role::where('name', 'coordinador_'.$name)->firstOrFail();

            //$permissions = Permission::all();
            $permissions = \TCG\Voyager\Models\Permission::where('key','browse_admin')
            ->orWhere('key','browse_instrumentos')
            ->orWhere('key','read_instrumentos')
            ->orWhere('key','browse_categorias_instrumentos')
            ->orWhere('key','read_categorias_instrumentos')
            ->orWhere('key','browse_categorias')
            ->orWhere('key','read_categorias')
            ->orWhere('key','read_instrumentos')
            ->orWhere('key','browse_indicadores')
            ->orWhere('key','read_indicadores')
            ->orWhere('key','browse_periodos_lectivos')
            ->orWhere('key','read_periodos_lectivos')
            ->orWhere('key','edit_periodos_lectivos')
            ->orWhere('key','add_periodos_lectivos')
            ->orWhere('key','ver_'.$name)
            ->orWhere('key','sincronizar_'.$name)
            ->orWhere('key','habilitar_evaluacion_'.$name)
            ->get();

            $role->permissions()->sync(
                $permissions->pluck('id')->all()
            );
        }

        
        //Asociamos todos los permisos al admin
        $role = Role::where('name', 'admin')->firstOrFail();
        $permissions = Permission::all();
        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );
    }
}
