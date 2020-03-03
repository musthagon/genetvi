<script>
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    
    $(document).ready(function () {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#periodos_lectivos").select2({
            placeholder: 'Seleccionar periodo lectivo a consultar',
        });

        $("#momentos_evaluacion").select2({
            placeholder: 'Seleccionar el momento evaluacion a consultar',
        });

        $("#instrumentos").select2({
            placeholder: 'Seleccionar un instrumento de evaluación a consultar',
        });

        $("#search_users").select2({
            language: {
                /*inputTooShort: function () {
                    return "Mínimo 4 caracteres";
                }*/
            },
            ajax: {
                
                url: "{{route('campus_users_by_ids')}}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        lastname: params.term, // search term
                        curso_id: {{$curso->getID()}},
                        periodo_lectivo_id: $("#periodos_lectivos").val(),
                        instrumento_id: $("#instrumentos").val(),
                        momento_evaluacion_id: $("#momentos_evaluacion").val(),
                        page: params.page || 1,
                    };
                },
                cache: true
            },
            placeholder: 'Ver revisiones públicas',
            minimumInputLength: 0,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (results) {
            if (results.loading) {
                return results.text;
            }

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar'><img src='" + results.profileimageurl + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "<div class='select2-result-repository__description'></div>" +
                    "</div>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__title").text(results.fullname);
            $container.find(".select2-result-repository__description").text(results.email);

            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.fullname || repo.text;
        }
        
        
    
    });
</script>