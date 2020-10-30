<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Setting;

class GenetviSettingsTableSeeder extends Seeder
{
    /**
     * Este seeder agrega o actualiza las configuraciones por defecto de GENETVI
     * Verificar el orden (site.tile, site.description)
     * @return void
     */
    public function run()
    {

        //Notificaciones
        $options = json_encode(array (
            "default" => "true",
            "options" => array(
                "true"   =>"Habilitado",
                "false"  =>"Deshabilitado")    
        ));


        //Site
        $setting = $this->findSetting('sitio.title');
        $setting->fill([
            'display_name' => __('Título del Sitio'),
            'value'        => __('GENETVI'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 1,
            'group'        => 'Sitio',
        ])->save();
   
        $setting = $this->findSetting('sitio.description');
        $setting->fill([
            'display_name' => __('Descripción del Sitio'),
            'value'        => __('Gestión de la Evaluación de Entornos Virtuales de Aprendizaje'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 2,
            'group'        => 'Sitio',
        ])->save();
		
		        
        //Admin
        $setting = $this->findSetting('admin.title');
        $setting->fill([
            'display_name' => __('Título del Panel Administrativo'),
            'value'        => 'GENETVI',
            'details'      => '',
            'type'         => 'text',
            'order'        => 1,
            'group'        => 'Admin',
        ])->save();
        
        $setting = $this->findSetting('admin.description');
        $setting->fill([
            'display_name' => __('Descripción del Panel Administrativo'),
            'value'        => __('Gestión de la Evaluación de Entornos Virtuales de Aprendizaje'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 2,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.url');
        $setting->fill([
            'display_name' => __('Dirección del Campus Virtual (moodle)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 3,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.token_endpoint');
        $setting->fill([
            'display_name' => __('Dirección para el acceso por token (www.campusvirtual.com/login/token.php)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 4,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.webservice_endpoint');
        $setting->fill([
            'display_name' => __('Dirección para el acceso al servicio web (www.campusvirtual.com/webservice/rest/server.php)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 5,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.webservice_endpoint1');
        $setting->fill([
            'display_name' => __('Dirección para el acceso al servicio web (www.campusvirtual.com/pluginfile.php/)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 6,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.webservice_endpoint2');
        $setting->fill([
            'display_name' => __('Dirección para el acceso al servicio web (www.campusvirtual.com/webservice/pluginfile.php)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 7,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.token_service');
        $setting->fill([
            'display_name' => __('Nombre corto del servicio web (principal)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 8,
            'group'        => 'Admin',
        ])->save();
        
		$setting = $this->findSetting('admin.token_service2');
        $setting->fill([
            'display_name' => __('Nombre corto del servicio web (secundario)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 9,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.token_access');
        $setting->fill([
            'display_name' => __('Código del token del servicio web (principal)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 10,
            'group'        => 'Admin',
        ])->save();
		
		$setting = $this->findSetting('admin.token_access2');
        $setting->fill([
            'display_name' => __('Código del token del servicio web (secundario)'),
            'value'        => __(''),
            'details'      => '',
            'type'         => 'text',
            'order'        => 11,
            'group'        => 'Admin',
        ])->save();
		
		
        $setting = $this->findSetting('admin.loader');
        $setting->fill([
            'display_name' => __('Gif de Carga del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 12,
            'group'        => 'Admin',
        ])->save();
        

        $setting = $this->findSetting('admin.icon_image');
        $setting->fill([
            'display_name' => __('Ícono del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 13,
            'group'        => 'Admin',
        ])->save();
        
        $setting = $this->findSetting('admin.bg_image');
        $setting->fill([
            'display_name' => __('Imagen de fondo del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 14,
            'group'        => 'Admin',
        ])->save();
        
        $setting = $this->findSetting('admin.creacion_de_roles_para_categorias');
        $setting->fill([
            'display_name' => __('Creación de roles/permisos para categorías o dependencias nuevas'),
            'value'        => false,
            'details'      => $options,
            'type'         => 'select_dropdown',
            'order'        => 15,
            'group'        => 'Admin',
        ])->save();
        

        $setting = $this->findSetting('notificaciones.avisos_inicio_evaluacion_docente');
        $setting->fill([
            'display_name' => __('Notificación de evaluaciones pendientes cuando se inicia la evaluación, dirigida a los docentes del curso'),
            'value'        => true,
            'details'      => $options,
            'type'         => 'select_dropdown',
            'order'        => 1,
            'group'        => 'Notificaciones',
        ])->save();

        $setting = $this->findSetting('notificaciones.avisos_inicio_evaluacion_estudiante');
        $setting->fill([
            'display_name' => __('Notificación de evaluaciones pendientes cuando se inicia la evaluación, dirigida al estudiante del curso'),
            'value'        => true,
            'details'      => $options,
            'type'         => 'select_dropdown',
            'order'        => 2,
            'group'        => 'Notificaciones',
        ])->save();

        $setting = $this->findSetting('notificaciones.avisos_fin_evaluacion_docente');
        $setting->fill([
            'display_name' => __('Notificación de evaluaciones pendientes antes de finalizar la fecha de la evaluación, dirigida a los docentes del curso'),
            'value'        => true,
            'details'      => $options,
            'type'         => 'select_dropdown',
            'order'        => 3,
            'group'        => 'Notificaciones',
        ])->save();
        
        $setting = $this->findSetting('notificaciones.avisos_fin_evaluacion_estudiante');
        $setting->fill([
            'display_name' => __('Notificación de evaluaciones pendientes antes de finalizar la fecha de la evaluación, dirigida al estudiante del curso'),
            'value'        => true,
            'details'      => $options,
            'type'         => 'select_dropdown',
            'order'        => 4,
            'group'        => 'Notificaciones',
        ])->save();
        
    }

    /**
     * [setting description].
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
