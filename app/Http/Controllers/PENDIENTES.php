form.validate({

errorPlacement: function errorPlacement(error, element) {},
  rules: {
    @foreach($instrumento->categorias as $categoria)
    @foreach($categoria->indicadores as $indicador)
      @if($indicador->requerido())
        '{{$indicador->id}}@if($indicador->multipleField())[]@endif' : {required :true},
      @endif
    @endforeach
    @endforeach               
},
highlight: function (element, errorClass, validClass) {
    var target;
    if ($(element).is('select')) {
        target = $(element).parent('div');
    } else {
        target = $(element);
    }
    target.addClass(errorClass).removeClass(validClass);
},
unhighlight: function (element, errorClass, validClass) {
    var target;
    if ($(element).is('select')) {
        target = $(element).parent('div');
    } else {
        target = $(element);
    }
    target.addClass(validClass).removeClass(errorClass);
},
});

form.validate({

errorPlacement: function errorPlacement(error, element) {},
  rules: {
    @foreach($instrumento->categorias as $categoria)
    @foreach($categoria->indicadores as $indicador)
      @if($indicador->requerido())
        '{{$indicador->id}}@if($indicador->multipleField())[]@endif' : {required :true},
      @endif
    @endforeach
    @endforeach               
},
highlight: function (element, errorClass, validClass) {
    var target;
    if ($(element).is('select')) {
        target = $(element).parent('div');
    } else {
        target = $(element);
    }
    target.addClass(errorClass).removeClass(validClass);
},
unhighlight: function (element, errorClass, validClass) {
    var target;
    if ($(element).is('select')) {
        target = $(element).parent('div');
    } else {
        target = $(element);
    }
    target.addClass(validClass).removeClass(errorClass);
},

});