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
        $setting = $this->findSetting('site.title');
        $setting->fill([
            'display_name' => __('voyager::seeders.settings.site.title'),
            'value'        => __('GENETVI'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 1,
            'group'        => 'Site',
        ])->save();
   
        $setting = $this->findSetting('site.description');
        $setting->fill([
            'display_name' => __('voyager::seeders.settings.site.description'),
            'value'        => __('Gestión de la Evaluación de Entornos Virtuales de Aprendizaje'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 2,
            'group'        => 'Site',
        ])->save();
        
        $setting = $this->findSetting('site.logo');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('voyager::seeders.settings.site.logo'),
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
                'group'        => 'Site',
            ])->save();
        }

        //Adicionales GENETVI
        $setting = $this->findSetting('site.CVUCV_ADMIN_TOKEN');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_ADMIN_TOKEN'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 4,
                'group'        => 'Site',
            ])->save();
        }
        
        $setting = $this->findSetting('site.CVUCV_GET_SITE_URL');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_SITE_URL'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 5,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.CVUCV_GET_USER_TOKEN');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_USER_TOKEN'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 6,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.CVUCV_GET_USER_TOKEN_SERVICE');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_USER_TOKEN_SERVICE'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 7,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.CVUCV_GET_WEBSERVICE_ENDPOINT');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_WEBSERVICE_ENDPOINT'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 8,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_WEBSERVICE_ENDPOINT1'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 9,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('CVUCV_GET_WEBSERVICE_ENDPOINT2'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text',
                'order'        => 10,
                'group'        => 'Site',
            ])->save();
        }

        //Admin
        $setting = $this->findSetting('admin.title');
        $setting->fill([
            'display_name' => __('voyager::seeders.settings.admin.title'),
            'value'        => 'GENETVI',
            'details'      => '',
            'type'         => 'text',
            'order'        => 1,
            'group'        => 'Admin',
        ])->save();
        
        $setting = $this->findSetting('admin.description');
        $setting->fill([
            'display_name' => __('voyager::seeders.settings.admin.description'),
            'value'        => __('Gestión de la Evaluación de Entornos Virtuales de Aprendizaje'),
            'details'      => '',
            'type'         => 'text',
            'order'        => 2,
            'group'        => 'Admin',
        ])->save();
        
        $setting = $this->findSetting('admin.loader');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('voyager::seeders.settings.admin.loader'),
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
                'group'        => 'Admin',
            ])->save();
        }

        $setting = $this->findSetting('admin.icon_image');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('voyager::seeders.settings.admin.icon_image'),
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 4,
                'group'        => 'Admin',
            ])->save();
        }

        $setting = $this->findSetting('admin.bg_image');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('voyager::seeders.settings.admin.background_image'),
                'value'        => '',
                'details'      => '',
                'type'         => 'image',
                'order'        => 5,
                'group'        => 'Admin',
            ])->save();
        }

        
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
