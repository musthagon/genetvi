<footer class="app-footer">
    <div class="site-footer-right"> 
        
        <!-- Default to the left -->
        <strong><a href="https://campusvirtual.ucv.ve/moodle/mod/page/view.php?id=13">SEDUCV</a> 2019.</strong>

        <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Licencia de Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png" /></a><br />Este obra está bajo una <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">licencia de Creative Commons Reconocimiento 4.0 Internacional</a>.
        
    </div>
    <div class="hidden site-footer-right">
        @if (rand(1,100) == 100)
            <i class="voyager-rum-1"></i> {{ __('voyager::theme.footer_copyright2') }}
        @else
            {!! __('voyager::theme.footer_copyright') !!} <a href="http://thecontrolgroup.com" target="_blank">The Control Group</a>
        @endif
        @php $version = Voyager::getVersion(); @endphp
        @if (!empty($version))
            - {{ $version }}
        @endif
    </div>
</footer>
