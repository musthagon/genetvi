<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\DataRow;

class GenetviDataTypesTableSeeder extends Seeder
{
    protected $indexCount = 0;
    protected $indexCount2 = 0;
    /**
     * Este seeder actualiza los dataType de Laravel Voyager
     */
    public function run()
    {
        /**
        * DataType para users
        * DataRows para users
        */
        $dataType = $this->dataType('slug', 'users');
        //if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'users',
                'display_name_singular' => __('Usuario'),
                'display_name_plural'   => __('Usuarios'),
                'icon'                  => 'voyager-person',
                'model_name'            => 'App\\User',
                'policy_name'           => 'TCG\\Voyager\\Policies\\UserPolicy',
                'controller'            => 'App\\Http\\Controllers\\Voyager\\VoyagerUserController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        //}

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'number','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'name');
        $this->filldataRow($dataRow,'text','Nombre',1,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'email');
        $this->filldataRow($dataRow,'text','Correo eléctronico',1,1,1,1,1,1,'{}',4);
        $dataRow = $this->dataRow($dataType, 'password');
        $this->filldataRow($dataRow,'password','Contraseña',1,0,0,1,1,0,'{}',5);
        $dataRow = $this->dataRow($dataType, 'remember_token');
        $this->filldataRow($dataRow,'text','ID',0,0,0,0,0,0,'{}',6);
        $dataRow = $this->dataRow($dataType, 'avatar');
        $this->filldataRow($dataRow,'image','Avatar',0,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'role_id');
        $this->filldataRow($dataRow,'text','Rol asignado',0,1,1,1,1,1,'{}',7);
        $dataRow = $this->dataRow($dataType, 'cvucv_id');
        $this->filldataRow($dataRow,'text','CVUCV ID',0,0,0,1,1,1,'{}',8);
        $dataRow = $this->dataRow($dataType, 'cvucv_username');
        $this->filldataRow($dataRow,'text','CVUCV Usuario',0,0,0,1,1,1,'{}',9);
        $dataRow = $this->dataRow($dataType, 'cvucv_firstname');
        $this->filldataRow($dataRow,'text','CVUCV Nombre',0,0,0,1,1,1,'{}',10);
        $dataRow = $this->dataRow($dataType, 'cvucv_lastname');
        $this->filldataRow($dataRow,'text','CVUCV Apellido',0,0,0,1,1,1,'{}',11);
        $dataRow = $this->dataRow($dataType, 'cvucv_suspended');
        $this->filldataRow($dataRow,'number','CVUCV Suspendido',0,0,0,1,1,1,'{}',12);
        $dataRow = $this->dataRow($dataType, 'user_belongsto_role_relationship');
        $this->filldataRow($dataRow,'relationship','Rol asignado',1,1,1,1,1,0,
        [
            'model'       => 'TCG\\Voyager\\Models\\Role',
            'table'       => 'roles',
            'type'        => 'belongsTo',
            'column'      => 'role_id',
            'key'         => 'id',
            'label'       => 'display_name',
            'pivot_table' => 'roles',
            'pivot'       => '0',
            'taggable'    => '0',
        ],13);
        $dataRow = $this->dataRow($dataType, 'user_belongstomany_role_relationship');
        $this->filldataRow($dataRow,'relationship','Roles asignados',0,0,0,1,1,0,
        [
            'model'       => 'TCG\\Voyager\\Models\\Role',
            'table'       => 'roles',
            'type'        => 'belongsToMany',
            'column'      => 'id',
            'key'         => 'id',
            'label'       => 'display_name',
            'pivot_table' => 'user_roles',
            'pivot'       => '1',
            'taggable'    => '0',
        ],14);
        $dataRow = $this->dataRow($dataType, 'settings');
        $this->filldataRow($dataRow,'hidden','Settings',0,0,0,0,0,0,'{}',15);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',16);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',17);

        /**
        * DataType para menus
        * DataRows para menus
        */
        $dataType = $this->dataType('slug', 'menus');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'menus',
                'display_name_singular' => __('voyager::seeders.data_types.menu.singular'),
                'display_name_plural'   => __('voyager::seeders.data_types.menu.plural'),
                'icon'                  => 'voyager-list',
                'model_name'            => 'TCG\\Voyager\\Models\\Menu',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'number','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'name');
        $this->filldataRow($dataRow,'text','Name',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',3);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',4);

        /**
        * DataType para roles
        * DataRows para roles
        */
        $dataType = $this->dataType('slug', 'roles');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'roles',
                'display_name_singular' => __('voyager::seeders.data_types.role.singular'),
                'display_name_plural'   => __('voyager::seeders.data_types.role.plural'),
                'icon'                  => 'voyager-lock',
                'model_name'            => 'TCG\\Voyager\\Models\\Role',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'number','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'name');
        $this->filldataRow($dataRow,'text','Name',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',3);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',4);
        $dataRow = $this->dataRow($dataType, 'display_name');
        $this->filldataRow($dataRow,'text','Display Name',1,1,1,1,1,1,'{}',5);

        /**
        * DataType para instrumentos
        * DataRows para instrumentos
        */
        $dataType = $this->dataType('slug', 'instrumentos');
        $this->filldataType($dataType,'instrumentos','Instrumento','Instrumentos','','App\\Instrumento','App\\Http\\Controllers\\Voyager\\InstrumentoController',1,'');
        
        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'nombre');
        $this->filldataRow($dataRow,'text','Nombre',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'nombre_corto');
        $this->filldataRow($dataRow,'text','Nombre Corto del Instrumento',1,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'habilitar');
        $this->filldataRow($dataRow,'select_dropdown','Habilitar',1,0,0,1,1,1,
        [
            "default"=>"1",
            "options"=>["0"=>"No","1"=>"Si"],
            "description"=>"Si no esta habilitado, los coordinadores no podrán elegir este instrumento para evaluar y si hay evaluaciones activas, se deshabilitaran (no se podrán hacer hasta que se actualice este atributo)"
        ],4);
        $dataRow = $this->dataRow($dataType, 'descripcion');
        $this->filldataRow($dataRow,'rich_text_box','Instrucciones que verán los evaluadores',1,1,1,1,1,1,["validation"=>["rule"=>"required"]],5);
        $dataRow = $this->dataRow($dataType, 'anonimo');
        $this->filldataRow($dataRow,'select_dropdown','Visibilidad de las respuestas de los evaluadores',1,0,0,1,1,1,["default"=>"0","options"=>["0"=>"Respuestas Visibles","1"=>"Respuestas no Visibles"],"description"=>"Los evaluadores veran una advertencia antes de iniciar la evaluacion que les indica si son anónimas o no sus respuestas"],6);
        $dataRow = $this->dataRow($dataType, 'puede_rechazar');
        $this->filldataRow($dataRow,'select_dropdown','Formato Invitación',1,0,0,1,1,1,["default"=>"0","options"=>["0"=>"Obligatoria","1"=>"El evaluador puede aceptar/rechazar hacer la evaluación"]],7);
        $dataRow = $this->dataRow($dataType, 'invitacion_automatica');
        $this->filldataRow($dataRow,'select_dropdown','Invitación Automática a los usuarios con los roles a quien va dirigido este instrumento',1,0,0,1,1,1,["description"=>"Si se elige el formato manual, cuando se inicie la evaluacion de un curso, se desplegará el listado de usuarios del Campus para invitar evaluadores","default"=>"1","options"=>["0"=>"No, debe invitar de forma manual a los evaluadores","1"=>"Si"]],8);
        $dataRow = $this->dataRow($dataType, 'opciones');
        $this->filldataRow($dataRow,'code_editor','Opciones',0,0,0,0,0,0,'{}',9);
        $dataRow = $this->dataRow($dataType, 'instrumento_belongstomany_cursos_participantes_role_relationship');
        $this->filldataRow($dataRow,'relationship','Dirigido a',0,1,1,1,1,1,["model"=>"App\\CursoParticipanteRol","table"=>"cursos_participantes_roles","type"=>"belongsToMany","column"=>"id","key"=>"id","label"=>"cvucv_name","pivot_table"=>"instrumentos_cursos_participantes_roles","pivot"=>"1","taggable"=>"0"],10);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',11);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',12);
        
        /**
        * DataType para indicadores
        * DataRows para indicadores
        */
        $dataType = $this->dataType('slug', 'indicadores');
        $this->filldataType($dataType,'indicadores','Indicador','Indicadores','','App\\Indicador','',1,'');

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'nombre');
        $this->filldataRow($dataRow,'text','Nombre del Indicador',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'descripcion');
        $this->filldataRow($dataRow,'text','Descripcion',0,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'requerido');
        $this->filldataRow($dataRow,'select_dropdown','Requerido',1,1,1,1,1,1,["default"=>"1","options"=>["0"=>"No","1"=>"Si"]],4);
        $dataRow = $this->dataRow($dataType, 'tipo');
        $this->filldataRow($dataRow,'select_dropdown','Tipo',1,1,1,1,1,1,["default"=>"text","options"=>["select_dropdown"=>"Select Dropdown","select_multiple"=>"Select Multiple","text"=>"Text","text_area"=>"Text Area","likert"=>"Escala de Likert (Siempre, a veces, nunca)"]],5);
        $dataRow = $this->dataRow($dataType, 'opciones');
        $this->filldataRow($dataRow,'code_editor','Opciones',0,1,1,1,1,1,'{}',6);
        $dataRow = $this->dataRow($dataType, 'orden');
        $this->filldataRow($dataRow,'number','Orden',0,0,0,0,0,0,'{}',7);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',8);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',9);
        
        /**
        * DataType para categorias
        * DataRows para categorias
        */
        $dataType = $this->dataType('slug', 'categorias');
        $this->filldataType($dataType,'categorias','Categoría','Categorías','','App\\Categoria','App\\Http\\Controllers\\Voyager\\CategoriaController',1,'');
        
        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'nombre');
        $this->filldataRow($dataRow,'text','Nombre',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'nombre_corto');
        $this->filldataRow($dataRow,'text','Nombre Corto de la Categoría',1,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'descripcion');
        $this->filldataRow($dataRow,'rich_text_box','Descripcion',0,1,1,1,1,1,'{}',4);
        $dataRow = $this->dataRow($dataType, 'opciones');
        $this->filldataRow($dataRow,'code_editor','Opciones',0,1,1,1,1,1,'{}',5);
        $dataRow = $this->dataRow($dataType, 'orden');
        $this->filldataRow($dataRow,'text','Orden',0,0,0,0,0,0,'{}',6);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',7);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',8);
        
        /**
        * DataType para periodos-lectivos
        * DataRows para periodos-lectivos
        */
        $dataType = $this->dataType('slug', 'periodos-lectivos');
        $this->filldataType($dataType,'periodos_lectivos','Periodo Lectivo','Periodos Lectivos','','App\\PeriodoLectivo','App\\Http\\Controllers\\Voyager\\PeriodoLectivoController',1,'');

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'nombre');
        $this->filldataRow($dataRow,'text','Nombre',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'descripcion');
        $this->filldataRow($dataRow,'rich_text_box','Descripción',0,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'fecha_inicio');
        $this->filldataRow($dataRow,'date','Fecha Inicio del Período Lectivo',1,1,1,1,1,1,["display"=>["width"=>"6"],"validation"=>["rule"=>"required"],"description"=>"Fechas de duración del período lectivo. La fecha de inicio debe ser menor a la fecha de fin"],4);
        $dataRow = $this->dataRow($dataType, 'fecha_fin');
        $this->filldataRow($dataRow,'date','Fecha Fin del Período Lectivo',1,1,1,1,1,1,["display"=>["width"=>"6"],"validation"=>["rule"=>"required"],"description"=>"Fechas de duración del período lectivo. La fecha de fin debe ser mayor a la fecha de inicio"],5);
        $dataRow = $this->dataRow($dataType, 'opciones');
        $this->filldataRow($dataRow,'code_editor','Opciones',0,0,0,0,0,0,'{}',6);
        $dataRow = $this->dataRow($dataType, 'momento_evaluacion_activo_id');
        $this->filldataRow($dataRow,'text','Momento Evaluación Activo Id',0,1,1,0,0,0,'{}',7);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',8);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',9);

        $dataType = $this->dataType('slug', 'rol-en-cursos');
        $this->filldataType($dataType,'cursos_participantes_roles','Rol en Cursos','Roles en Cursos','','App\\CursoParticipanteRol','',1,'');

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'cvucv_name');
        $this->filldataRow($dataRow,'text','Nombre del rol en el CVUCV ',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'cvucv_shortname');
        $this->filldataRow($dataRow,'text','Nombre corto del rol en el CVUCV',1,1,1,1,1,0,'{}',3);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',4);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',5);
      
        $dataType = $this->dataType('slug', 'momentos-evaluacion');
        $this->filldataType($dataType,'momentos_evaluacion','Momento para la Evaluación','Momentos para las Evaluaciones','','App\\MomentosEvaluacion','',1,'');

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,0,0,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'nombre');
        $this->filldataRow($dataRow,'text','Nombre',1,1,1,1,1,1,["display"=>["width"=>"6"]],2);
        $dataRow = $this->dataRow($dataType, 'nombre_corto');
        $this->filldataRow($dataRow,'text','Nombre Corto',1,1,1,1,1,1,["display"=>["width"=>"6"]],3);
        $dataRow = $this->dataRow($dataType, 'descripcion');
        $this->filldataRow($dataRow,'rich_text_box','Descripcion',0,1,1,1,1,1,'{}',4);
        $dataRow = $this->dataRow($dataType, 'opciones');
        $this->filldataRow($dataRow,'code_editor','Opciones',0,1,1,1,1,1,'{}',5);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,1,1,0,0,0,'{}',6);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,1,1,0,0,0,'{}',7);

        /*$dataType = $this->dataType('slug', 'invitaciones');
        $this->filldataType($dataType,'invitaciones','Invitación','Invitaciones','','App\\Invitacion','',1,'');

        $dataRow = $this->dataRow($dataType, 'id');
        $this->filldataRow($dataRow,'text','ID',1,1,1,0,0,0,'{}',1);
        $dataRow = $this->dataRow($dataType, 'estatus_invitacion_id');
        $this->filldataRow($dataRow,'text','Estatus Invitacion Id',1,1,1,1,1,1,'{}',2);
        $dataRow = $this->dataRow($dataType, 'tipo_invitacion_id');
        $this->filldataRow($dataRow,'text','Tipo Invitacion Id',1,1,1,1,1,1,'{}',3);
        $dataRow = $this->dataRow($dataType, 'instrumento_id');
        $this->filldataRow($dataRow,'text','Instrumento Id',1,1,1,1,1,1,'{}',4);
        $dataRow = $this->dataRow($dataType, 'curso_id');
        $this->filldataRow($dataRow,'text','Curso Id',1,1,1,1,1,1,'{}',5);
        $dataRow = $this->dataRow($dataType, 'periodo_lectivo_id');
        $this->filldataRow($dataRow,'text','Periodo Lectivo Id',1,1,1,1,1,1,'{}',6);
        $dataRow = $this->dataRow($dataType, 'token');
        $this->filldataRow($dataRow,'text','Token',1,1,1,1,1,1,'{}',7);
        $dataRow = $this->dataRow($dataType, 'cvucv_user_id');
        $this->filldataRow($dataRow,'text','CVUCV User Id',1,1,1,1,1,1,'{}',8);
        $dataRow = $this->dataRow($dataType, 'usuario_id');
        $this->filldataRow($dataRow,'text','Usuario Id',0,0,0,0,0,0,'{}',9);
        $dataRow = $this->dataRow($dataType, 'cantidad_recordatorios');
        $this->filldataRow($dataRow,'text','Cantidad Recordatorios',1,1,1,1,1,1,'{}',10);
        $dataRow = $this->dataRow($dataType, 'created_at');
        $this->filldataRow($dataRow,'timestamp','Created At',0,0,0,0,0,0,'{}',11);
        $dataRow = $this->dataRow($dataType, 'updated_at');
        $this->filldataRow($dataRow,'timestamp','Updated At',0,0,0,0,0,0,'{}',12);
        $dataRow = $this->dataRow($dataType, 'invitacione_belongsto_estatus_invitacione_relationship');
        $this->filldataRow($dataRow,'relationship','estatus_invitaciones',0,1,1,1,1,1,["model"=>"App\\\\EstatusInvitacion","table"=>"estatus_invitaciones","type"=>"belongsTo","column"=>"estatus_invitacion_id","key"=>"id","label"=>"nombre","pivot_table"=>"categorias","pivot"=>"0","taggable"=>"0"],13);
        $dataRow = $this->dataRow($dataType, 'invitacione_belongsto_tipo_invitacione_relationship');
        $this->filldataRow($dataRow,'relationship','tipo_invitaciones',0,1,1,1,1,1,["model"=>"App\\\\TipoInvitacion","table"=>"tipo_invitaciones","type"=>"belongsTo","column"=>"tipo_invitacion_id","key"=>"id","label"=>"nombre","pivot_table"=>"categorias","pivot"=>"0","taggable"=>"0"],14);
        $dataRow = $this->dataRow($dataType, 'invitacione_belongsto_instrumento_relationship');
        $this->filldataRow($dataRow,'relationship','instrumentos',0,1,1,1,1,1,["model"=>"App\\\\Instrumento","table"=>"instrumentos","type"=>"belongsTo","column"=>"instrumento_id","key"=>"id","label"=>"nombre","pivot_table"=>"categorias","pivot"=>"0","taggable"=>"0"],15);
        $dataRow = $this->dataRow($dataType, 'invitacione_belongsto_curso_relationship');
        $this->filldataRow($dataRow,'relationship','cursos',0,1,1,1,1,1,["model"=>"App\\\\Curso","table"=>"cursos","type"=>"belongsTo","column"=>"curso_id","key"=>"id","label"=>"cvucv_fullname","pivot_table"=>"categorias","pivot"=>"0","taggable"=>"0"],16);
        $dataRow = $this->dataRow($dataType, 'invitacione_belongsto_periodos_lectivo_relationship');
        $this->filldataRow($dataRow,'relationship','periodos_lectivos',0,1,1,1,1,1,["model"=>"App\\\\PeriodoLectivo","table"=>"periodos_lectivos","type"=>"belongsTo","column"=>"periodo_lectivo_id","key"=>"id","label"=>"nombre","pivot_table"=>"categorias","pivot"=>"0","taggable"=>"0"],17);
            */
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        $this->indexCount = $this->indexCount + 1;
        return DataType::firstOrNew([
            $field => $for,
            'id' => $this->indexCount
        ]);

    }

    protected function filldataType($dataType,$name, $display_name_singular, $display_name_plural,$icon, $model_name, $controller, $generate_permissions, $description)
    {
        $dataType->fill([
            'name'                  => $name,
            'display_name_singular' => __($display_name_singular),
            'display_name_plural'   => __($display_name_plural),
            'icon'                  => $icon,
            'model_name'            => $model_name,
            'controller'            => $controller,
            'generate_permissions'  => $generate_permissions,
            'description'           => $description,
        ])->save();
    }
    /**
     * [dataRow description].
     *
     * @param [type] $type  [description]
     * @param [type] $field [description]
     *
     * @return [type] [description]
     */
    protected function dataRow($type, $field)
    {
        return DataRow::firstOrNew([
            'data_type_id' => $type->id,
            'field'        => $field,
        ]);
    }

    protected function filldataRow($dataRow, $type, $display_name, $required, $browse, $read, $edit, $add, $delete, $details, $order)
    {
        $this->indexCount2 = $this->indexCount2 + 1;
        $dataRow->fill([
            'id' => $this->indexCount2,
            'type' => $type,
            'display_name' => $display_name,
            'required' => $required,
            'browse' => $browse,
            'read' => $read,
            'edit' => $edit,
            'add' => $add,
            'delete' => $delete,
            'details' => $details,
            'order' => $order,
        ])->save();
    }
    
}
