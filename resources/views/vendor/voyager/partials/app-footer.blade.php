<footer class="app-footer">
    <div class="site-footer-right"> 
        
        <!-- Default to the left -->
        <strong><a target="_blank" href="https://campusvirtual.ucv.ve/moodle/mod/page/view.php?id=13">SEDUCV</a> 2019.</strong>

        <a rel="license" target="_blank" href="https://www.gnu.org/licenses/gpl-3.0.html"></a><br />Este obra está bajo una licencia <a rel="license" target="_blank" href="https://www.gnu.org/licenses/gpl-3.0.html">GNU General Public License</a>.
    
    </div>
    <div class="site-footer-right">
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
