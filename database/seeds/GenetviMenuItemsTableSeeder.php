<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class GenetviMenuItemsTableSeeder extends Seeder
{   
    protected $indexCount = 0;

    /**
     * Este seeder agrega o actualiza los item de los menu
     * 
     * @return void
     */
    public function run()
    {
        //Menu administrador
        $menu = Menu::firstOrNew([
            'name' => 'admin',
            'id' => 1,
        ]);

        $this->fillItem($menu->id, 'Panel Administrativo',  '', 'voyager.dashboard', '_self','voyager-boat', '#000000', null, 1);
        $RolesItem = $this->fillItem($menu->id, 'Roles','','', '_self','voyager-lock', '#000000', null, 2);
            $this->fillItem($menu->id, 'Roles', '','voyager.roles.index', '_self','voyager-lock', '#000000', $RolesItem, 1);
            $this->fillItem($menu->id, 'Roles en Cursos','','voyager.rol-en-cursos.index', '_self','voyager-people', '#000000', $RolesItem, 2);
        $this->fillItem($menu->id, 'Usuarios',  '', 'voyager.users.index', '_self','voyager-person', '#000000', null, 3);
        $this->fillItem($menu->id, 'Almacenamiento',  '', 'voyager.media.index', '_self','voyager-images', '#000000', null, 4);
        $toolsMenuItem = $this->fillItem($menu->id, 'Herramientas', '', '', '_self','voyager-tools', '#000000', null, 5);
            $this->fillItem($menu->id, 'voyager::seeders.menu_items.menu_builder','','voyager.menus.index', '_self','voyager-list', '#000000', $toolsMenuItem, 1);
            $this->fillItem($menu->id, 'Base de Datos','','voyager.database.index', '_self','voyager-data', '#000000', $toolsMenuItem, 2);
            $this->fillItem($menu->id, 'voyager::seeders.menu_items.compass','','voyager.compass.index', '_self','voyager-compass', '#000000', $toolsMenuItem, 3);
            $this->fillItem($menu->id, 'voyager::seeders.menu_items.bread','','voyager.bread.index', '_self','voyager-bread', '#000000', $toolsMenuItem, 4);
            $this->fillItem($menu->id, 'Hooks','','voyager.hooks', '_self','voyager-hook', '#000000', $toolsMenuItem, 5);
            $this->fillItem($menu->id, 'Opciones','','voyager.settings.index', '_self','voyager-settings', '#000000', $toolsMenuItem, 6);
        $instrumentosMenuItem = $this->fillItem($menu->id, 'Instrumentos para EVA',  '', '', '_self','voyager-documentation', '#80ffff', null, 6);
            $this->fillItem($menu->id, 'Instrumentos','','voyager.instrumentos.index', '_self','', '#000000', $instrumentosMenuItem, 1);
            $this->fillItem($menu->id, 'Categorías','','voyager.categorias.index', '_self','', '#000000', $instrumentosMenuItem, 2);
            $this->fillItem($menu->id, 'Indicadores','','voyager.indicadores.index', '_self','', '#000000', $instrumentosMenuItem, 3);
        $EvaluacionItem = $this->fillItem($menu->id, 'Evaluación', '', '', '_self','voyager-calendar', '#0080ff', null, 7);
            $this->fillItem($menu->id, 'Periodos Lectivos','','voyager.periodos-lectivos.index', '_self','', '#0080ff', $EvaluacionItem, 1);
            $this->fillItem($menu->id, 'Momentos para las Evaluaciones','','voyager.momentos-evaluacion.index', '_self','', '#000000', $EvaluacionItem, 2);
        $this->fillItem($menu->id, 'Cursos del CVUCV',  '', 'gestion.evaluaciones', '_self','voyager-hammer', '#ffff00', null, 8);  

        //Menu Usuario
        $user_menu = Menu::firstOrNew([
            'name' => 'user_menu',
            'id' => 2,
        ]);
        $this->fillItem($user_menu->id, 'Principal',  '/home', '', '_self','fa fa-home', '#ffffff', null, 1);  
        $this->fillItem($user_menu->id, 'Cursos',  '/mis_cursos', '', '_self','fa fa-files-o', '#ffffff', null, 2); 
        $this->fillItem($user_menu->id, 'Panel Administrativo',  '/admin', '', '_self','fa fa-dashboard', '#ffffff', null, 3); 
    }
    /**
     * [setting description].
     *
     * @param $menu_id , $item_id
     *
     * @return $id
     */
    protected function findItem($menu_id)
    {
        $this->indexCount = $this->indexCount + 1;
        return MenuItem::firstOrNew([
            'menu_id' => $menu_id,
            'id' => $this->indexCount
        ]);
    }


    protected function fillItem($menu, $title, $url, $route, $target, $icon_class, $color, $parent_id, $order)
    {
        $menuItem = $this->findItem($menu);

        $menuItem->fill([    
            'title' => __($title),
            'url' => $url,
            'route' => $route,
            'target' => $target,
            'icon_class' => $icon_class,
            'color' => $color,
            'parent_id'  => $parent_id,
            'order' => $order,
        ])->save();

        return $menuItem->id;
    }
}

