
(function () {

    /* global $ */
    // var aliasok = true;
    // var emailok = false;
    // var usuario = null
    //  data = "";
    var orden = 'c.categoria';
    var pagina = 1;
    var filtro = null;
    // $('#añadirLinks').on('click', function(event){
    //     event.preventDefault();
    //     data = $(event.currentTarget).attr('data-lista');
    //     pagina = $(event.currentTarget).attr('data-pagina');
    //     dataorden = $(event.currentTarget).attr('data-orden');
    //     history.pushState(null, "", 'index/añadirLink');
    //     // getListado(data,pagina,dataorden);
    // });
    
   $('.ordenar').on('click', function(e) {
            e.preventDefault();
            orden = e.target.getAttribute('data-orden');
            getListado(); 
        });
        
    $('.borrar').on('click', function(e) {
            e.preventDefault();
            let id = e.target.getAttribute('data-id');
            borrar(id); 
    });
   
    $('.btnPagina').on('click', function(e) {
            e.preventDefault();
            pagina = e.target.getAttribute('data-pagina');
            getListado(); 
        });
        
     $('#filtroBt').on('click', function(e) {
            e.preventDefault();
            filtro = document.getElementById('filtro').value;
            pagina = 1;
            getListado(); 
        });
    
    
    $('#addcategoria').on('click', function(event) {
         event.preventDefault();
       
        var categoria = $('#nuevacategoria').val().trim()
        if(categoria !== '' ) {
            genericAjax('ajax/addcategory', {'categoria': categoria}, 'get', function(json) {
                console.log(json);
                var option = "<option value='"+json.id_categoria+"'>"+json.nombre+"</option>"
                $('#listaSelect').append(option);
                
            });
        }
    });
    
     
    $('#addenlace').on('click', function(event) {
        event.preventDefault();
        var select = document.getElementById("listaSelect")
        var selectedOption = select.options[select.selectedIndex];
        // console.log(selectedOption.value + ': ' + selectedOption.text);
        var parametros = {
                href            : $('#url').val().trim(),
                comentario      : $('#comentario').val().trim(),
                categoria : selectedOption.value
            };
   
        if(parametros.categoria !== '' && parametros.href !== '' && parametros.comentario !== '') {
            genericAjax('ajax/addlink',parametros, 'get', function(json) {
                console.log(json);
                if(json.resultado>0){
                    alert("insertado con exito");
                }else{
                    alert("error al insertar");
                }
            });
        }
        
    });
    
    
    $(document).ajaxStart(function () {
        $('.wrapper-loader').removeClass('hidden');
    });

    $(document).ajaxStop(function () {
        $('.wrapper-loader').addClass('hidden');
    });
  
    var genericAjax = function (url, data, type, callBack) {
        $.ajax({
            url: url,
            data: data,
            type: type,
            dataType : 'json',
        })
        .done(function( json ) {
            console.log('ajax done');
            console.log(json);
            callBack(json);
        })
        .fail(function( xhr, status, errorThrown ) {
            console.log('ajax fail');
        })
        .always(function( xhr, status ) {
            console.log('ajax always');
        });
    }
    
    var borrar = function (id) {      
  
                genericAjax('ajax/borrar', {'id': id }, 'get', function(json) {
                    if(json.result===1){
                        alert("borrado correctamente");
                        getListado();
                    }else{
                        alert("Usuario no borrado");
                    }
                    
                    
                });
               
    }
    
    
    var getListado = function () {      
  
                genericAjax('ajax/listarLinks', {'pagina': pagina ,'orden' : orden, 'filtro' : filtro}, 'get', function(json) {
                    console.log(json);
                    pintar(json.link);
                    procesarPaginas(json.paginas);
                });
               
    }

    // var getHeader = function (objeto) {
    //     let result = '<tr>';
    //     console.log(objeto);
    //     // $.each(objeto, (key, value) => {
    //         var value = objeto[0];
    //         $.each(value, (key2,value2) => {
    //             if(key2 != "id"){
    //               result += '<th><a href="#" data-orden='+key2+'>' + key2 + '</a></th>';  
    //             }
                  
    //         });
         
    //     // });
    //     result += '<td>Editar</td>'; 
    //     result += '<td>Borrar</td>'; 
    //     result += '</tr>';
    //     return result;
    // }

    var getBody = function (objeto) {
        let result = '<tr>';
        $.each(objeto, (key, value) => {
            if(key != "id"){
               result += '<td>' + value.nombrecategoria + '</td>';    
               result += '<td>' + value.href + '</td>'; 
               result += '<td>' + value.comentario + '</td>'; 
            }
              
        });
        result += '<td><button class="borrar" data-id='+objeto.links.id+'>Borrar</button></td>'; 
        
        
        result += '</tr>';
        return result;
    }

    var pintar = function (objeto) {
        var listaitems = '';
        $.each(objeto, (key, value)  =>{
            listaitems += getBody(value);
        });
        var tabla = '<tbody class="table_body"></tbody>'
        $('.table_body').empty();
        $('.table_body').append(listaitems);
        // $('.main').append(tabla);
        // $('.main').append(div);
        // $('.table_thead').empty();
        // $('.table_thead').append(header);
        // $('.table_body').empty();
        // $('.table_body').append(listaitems);
    }
    
    var procesarPaginas = function (paginas) {
        var stringFirst = '<div class="col-md-3"><a href = "#" class = "btnPagina2 btn btn-primary" data-pagina='+paginas.primero+'> < </a></div>';
        var stringPrev  = '<div class="col-md-3"><a href = "#" class = "btnPagina2 btn btn-primary" data-pagina='+paginas.anterior+'>anterior</a></div>';
        var stringRange = '';
        // $.each(paginas.rango, function(key, value) {
        //     if(paginas.pagina == value) {
        //         stringRange += '<a href = "#" class = "btnPagina btn btn-info">' + value + '</a> ';
        //     } else {
        //         stringRange += '<a href = "#" class = "btnPagina btn btn-primary" data-pagina="' + value + '">' + value + '</a> ';
        //     }
        // });
        var stringNext = '<div class="col-md-3"><a href = "#" class = "btnPagina2 btn btn-primary" data-pagina='+paginas.siguiente+'>siguiente</a></div>';
        var stringLast = '<div class="col-md-3"><a href = "#" class = "btnPagina2 btn btn-primary" data-pagina='+paginas.ultimo+'> s > s </a></div>';
        var finalString = stringFirst + stringPrev + stringRange + stringNext + stringLast;
        $('#pintarPaginas').empty();
        $('#pintarPaginas').append(finalString);
        $('.btnPagina2').on('click', function(e) {
            e.preventDefault();
            pagina = e.target.getAttribute('data-pagina');
            getListado(); 
        });
    //     $('.btnPagina').on('click', function(e) {
    //         e.preventDefault();
    //         var pagina = e.target.getAttribute('data-pagina');
    //         var orden = 'c.categoria';
    //         getListado(pagina,orden); 
    //     });
    //     $('.btnNoPagina').on('click', function(e) {
    //         e.preventDefault();
    //     });
        
    //     $('.table_thead tr th a').on('click', function(event){
    //     event.preventDefault();
    //     dataorden = $(event.currentTarget).attr('data-orden');
    //     history.pushState(null, "", 'admin/'+data);
    //     getListado(data,pagina,dataorden);
    // })
    
    // $('.borrar-user').on('click', function(event){
    //     event.preventDefault();
    //     id = $(event.currentTarget).attr('data-id');
    //     if(confirm("¿Seguro que quieres borrar al usuario cuya id es :" +id+"?")){
            
        
    //     history.pushState(null, "", 'admin/borrar'+id);
    //      genericAjax('ajax/deleteuser', {'id': id }, 'get', function(json) {
    //                 getListado(data,pagina,dataorden)
    //             });
    //     }
        
    // })
    
    
    
    }
    
})();
