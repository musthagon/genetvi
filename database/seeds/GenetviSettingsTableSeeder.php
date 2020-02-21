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
        
        //Adicionales GENETVI
        $setting = $this->findSetting('sitio.instrucciones');
        $setting->fill([
            'display_name' => __('Instrucciones de cómo usar la aplicación'),
            'value'        => '
                <div class="row">
                <div class="col-md-12">
                <!-- Custom Tabs (Pulled to the right) -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                    <li class="pull-left header"><p class="fa fa-th"></p>Instrucciones para Profesores</li>
                    <li class="active"><a href="#tab_1_1" data-toggle="tab" aria-expanded="true">¿Cómo veo mis cursos?</a></li>
                    <li class=""><a href="#tab_2_2" data-toggle="tab" aria-expanded="false">¿Cómo puedo evaluar algún curso?</a></li>
                    <li class=""><a href="#tab_3_2" data-toggle="tab" aria-expanded="false">¿Cómo veo quienes han evaluado mis cursos?</a></li>              
                    </ul>
                    <div class="tab-content">
                    <div class="tab-pane active" id="tab_1_1">
                        <b>How to use:</b>
        
                        <p>Exactly like the original bootstrap tabs except you should use
                        the custom wrapper <code>.nav-tabs-custom</code> to achieve this style.</p>
                        A wonderful serenity has taken possession of my entire soul,
                        like these sweet mornings of spring which I enjoy with my whole heart.
                        I am alone, and feel the charm of existence in this spot,
                        which was created for the bliss of souls like mine. I am so happy,
                        my dear friend, so absorbed in the exquisite sense of mere tranquil existence,
                        that I neglect my talents. I should be incapable of drawing a single stroke
                        at the present moment; and yet I feel that I never was a greater artist than now.
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2_2">
                        The European languages are members of the same family. Their separate existence is a myth.
                        For science, music, sport, etc, Europe uses the same vocabulary. The languages only differ
                        in their grammar, their pronunciation and their most common words. Everyone realizes why a
                        new common language would be desirable: one could refuse to pay expensive translators. To
                        achieve this, it would be necessary to have uniform grammar, pronunciation and more common
                        words. If several languages coalesce, the grammar of the resulting language is more simple
                        and regular than that of the individual languages.
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3_2">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                        It has survived not only five centuries, but also the leap into electronic typesetting,
                        remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
                        sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
                        like Aldus PageMaker including versions of Lorem Ipsum.
                    </div>
                    <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
        ',                                            //Actualizar!!!!!!
            'details'      => '',
            'type'         => 'rich_text_box',
            'order'        => 4,
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
        
        $setting = $this->findSetting('admin.loader');
        $setting->fill([
            'display_name' => __('Gif de Carga del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 3,
            'group'        => 'Admin',
        ])->save();
        

        $setting = $this->findSetting('admin.icon_image');
        $setting->fill([
            'display_name' => __('Ícono del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 4,
            'group'        => 'Admin',
        ])->save();
        

        $setting = $this->findSetting('admin.bg_image');
        $setting->fill([
            'display_name' => __('Imagen de fondo del Panel Administrativo'),
            'value'        => '',
            'details'      => '',
            'type'         => 'image',
            'order'        => 5,
            'group'        => 'Admin',
        ])->save();
        

        //Notificaciones
        $options = json_encode(array (
            "default" => "true",
            "options" => array(
                "true"   =>"Habilitado",
                "false"  =>"Deshabilitado")    
        ));

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
