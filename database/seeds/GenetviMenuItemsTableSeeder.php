<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class GenetviMenuItemsTableSeeder extends Seeder
{
    /**
     * Este seeder agrega o actualiza los item de los menu
     * 
     * @return void
     */
    public function run()
    {
        //Menu administrador
        $menu = Menu::where('name', 'admin')->firstOrFail();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 1 
        ]);
        $menuItem->fill([    
            'title'   => __('Panel Administrativo'),
            'url'     => '',
            'route'   => 'voyager.dashboard',
            'target'     => '_self',
            'icon_class' => 'voyager-boat',
            'color'      => '#000000',
            'parent_id'  => null,
            'order'      => 1,
        ])->save();
        
        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 2 
        ]);
        $menuItem->fill([    
            'title'   => __('Almacenamiento'),
            'url'     => '',
            'route'   => 'voyager.media.index',
            'target'     => '_self',
            'icon_class' => 'voyager-images',
            'color'      => '#000000',
            'parent_id'  => null,
            'order'      => 4,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 3 
        ]);
        $menuItem->fill([    
            'title'   => __('Usuarios'),
            'url'     => '',
            'route'   => 'voyager.users.index',
            'target'     => '_self',
            'icon_class' => 'voyager-person',
            'color'      => '#000000',
            'parent_id'  => null,
            'order'      => 3,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 4 
        ]);
        $menuItem->fill([    
            'title'   => __('Roles'),
            'url'     => '',
            'route'   => 'voyager.roles.index',
            'target'     => '_self',
            'icon_class' => 'voyager-lock',
            'color'      => '#000000',
            'parent_id'  => null,
            'order'      => 31,
        ])->save();

        $toolsMenuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 5 
        ]);
        $toolsMenuItem->fill([    
            'title'   => __('Herramientas'),
            'url'     => '',
            'route'   => 'voyager.roles.index',
            'target'     => '_self',
            'icon_class' => 'voyager-tools',
            'color'      => '#000000',
            'parent_id'  => null,
            'order'      => 5,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 6
        ]);
        $menuItem->fill([    
            'title'   => __('voyager::seeders.menu_items.menu_builder'),
            'url'     => '',
            'route'   => 'voyager.menus.index',
            'target'     => '_self',
            'icon_class' => 'voyager-list',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 1,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 7
        ]);
        $menuItem->fill([    
            'title'   => __('Base de Datos'),
            'url'     => '',
            'route'   => 'voyager.database.index',
            'target'     => '_self',
            'icon_class' => 'voyager-data',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 2,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 8
        ]);
        $menuItem->fill([    
            'title'   => __('voyager::seeders.menu_items.compass'),
            'url'     => '',
            'route'   => 'voyager.compass.index',
            'target'     => '_self',
            'icon_class' => 'voyager-compass',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 3,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 9
        ]);
        $menuItem->fill([    
            'title'   => __('voyager::seeders.menu_items.bread'),
            'url'     => '',
            'route'   => 'voyager.bread.index',
            'target'     => '_self',
            'icon_class' => 'voyager-bread',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 4,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 10
        ]);
        $menuItem->fill([    
            'title'   => __('Opciones'),
            'url'     => '',
            'route'   => 'voyager.settings.index',
            'target'     => '_self',
            'icon_class' => 'voyager-settings',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 6,
        ])->save();

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 11
        ]);
        $menuItem->fill([    
            'title'   => __('Hooks'),
            'url'     => '',
            'route'   => 'voyager.hooks',
            'target'     => '_self',
            'icon_class' => 'voyager-hook',
            'color'      => '#000000',
            'parent_id'  => $toolsMenuItem->id,
            'order'      => 5,
        ])->save();

        /***
         * * Adicionales GENETVI
         * */ 
        $instrumentosMenuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'id' => 18
        ]);
        $instrumentosMenuItem->fill([    
            'title'   => __('Instrumentos para EVA'),
            'url'     => '',
            'route'   => '',
            'target'     => '_self',
            'icon_class' => 'voyager-documentation',
            'color'      => '#80ffff',
            'parent_id'  => null,
            'order'      => 6,
        ])->save();

    }
}

