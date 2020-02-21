<ul class="sidebar-menu" data-widget="tree" >

    <li class="header">Menu de Navegación</li>

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
        @if( ($menu_item->title != "Panel Administrativo") || 
             (($menu_item->title == "Panel Administrativo") && Auth::user()->hasPermission('browse_admin')) )
            <li class="@if($hijos) treeview  @endif {{ $isActive }}">
                <a href="{{ $menu_item->url }}" target="{{ $menu_item->target }}" style="{{ $styles }}">
                    {!! $icon !!}

                    @if($menu_item->parent_id == null || $hijos)
                        <span>{{ $menu_item->title }}</span>
                    @else
                        {{ $menu_item->title }}
                    @endif

                    @if($hijos)
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    @endif
                </a>
                @if($hijos)
                    @include('user_menu2', ['items' => $menu_item->children])
                @endif
            </li>
        @endif
    @endforeach

    
</ul>
