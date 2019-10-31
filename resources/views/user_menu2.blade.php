<ul class="treeview-menu">
        
    @foreach($items as $menu_item)

    @php
    
        $isActive = null;
        $styles = null;
        $icon = null;

        // Background Color or Color
        if (isset($menu_item->color)) {
            $styles = 'color:'.$menu_item->color;
        }
        if (isset($menu_item->background)) {
            $styles = 'background-color:'.$menu_item->color;
        }

        // Check if link is current
        if(url($menu_item->link()) == url()->current()){
            $isActive = 'active';
        }

        // Set Icon
        if(isset($menu_item->icon_class) ){
            $icon = '<i class="' . $menu_item->icon_class . '"></i>';
        }

        //Tiene hijos?
        $hijos = false;
        if(!$menu_item->children->isEmpty()){
            $hijos = true;
        }
    @endphp

        <li class="{{ $isActive }} @if($hijos) treeview @endif">
            <a href="{{ $menu_item->url }}" target="{{ $menu_item->target }}" style="{{ $styles }}">
                {!! $icon !!}
                
                @if($hijos)
                    <span>{{ $menu_item->title }}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                @else
                    {{ $menu_item->title }}
                @endif
            </a>
            @if($hijos)
                @include('user_menu2', ['items' => $menu_item->children])
            @endif
        </li>
        
    @endforeach
</ul>

<!--<ul class="sidebar-menu" data-widget="tree">
    <li class="header">Opciones</li>

    <li class="active"><a href="#"><i class="fa fa-link"></i> <span>Link</span></a></li>
    <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>

    <li class="treeview">
        <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="#"><i class="fa fa-circle-o"></i>Link in level 2</a></li>

            <li class="treeview">
                <a href="#"><i class="fa fa-circle-o"></i><span>Link in level 3</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i>Link in level 3</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i>Link in level 3</a></li>
                </ul>
            </li>

        </ul>
    </li>
</ul>-->