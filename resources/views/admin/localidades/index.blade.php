@extends('adminlte::page')

@section('title', 'Localidades')

@section('content_header')
<button type="button" name="FormLoc" id="FormLoc" class="btn btn-success"> <i class="fas fa-fw fa-plus"></i>Nueva Localidad</button>
    <p></p>
    <h1 class="text-center">Lista de Localidades de Ocosingo</h1>
@stop

@section('content')
<!--TABLA DE VISTA DE CONSULTA DE AREAS REGISTRADAS-->
<table class="table table-striped table-bordered localidades"> 
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre Localidades</th>
            <th>Nombre Region</th>
            <th>Fecha Creacion</th>
            <th>Fecha Actualizacion</th>
            <th width="150px">Opciones</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<!--modal eliminar una localidad empieza-->
<div class="modal fade" id="ModalEliminar" tabindex="-1" aria-labelledby="ModalLabel" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="form_eliminar" class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 id="loc_editar" name="loc_editar"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-fw fa-times-circle"></i>Cancelar</button>
                    <button type="button" name="btnElLoc" id="btnElLoc" class="btn btn-danger"><i class="fas fa-fw fa-check"></i>Eliminar</button>
                </div>
            </form>  
        </div>
    </div>
</div>

<!--modal agregar localidades-->
<div id="ModalAgregar" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="form_loc" name="form_loc" class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <div class="form-group" style="display:none;">
                        <label>Apartado de Seccion de Verificacion de Tipo de Proceso</label>
                        <input type="text" name="action" id="action" value="Add" />
                        <input type="text" name="hidden_id" id="hidden_id"  />
                    </div>
                    <div class="form-group">
                        <label>Nombre Localidad : </label>
                        <input type="text" name="Nom_Loc" id="Nom_Loc" class="form-control"/>

                    </div>
                    <div class="form-group">
                        <label>Regiones</label><br>
                        <select class="form-control" id="Id_Reg" name="Id_Reg">
                            <option value="0" selected>Selecciona una Region</option>
                            @foreach($regiones as $region)
                                <option value="{{$region->id}}">{{$region->Nom_Reg}}</option>
                            @endforeach  
                        </select>
                    </div>

                    <span id="form_result"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-fw fa-times-circle"></i>Cancelar</button>
                    <input type="submit" class="btn btn-primary" name="btnGuardarLoc" id="btnGuardarLoc" value="Guardar" />
                </div>
            </form>  
        </div>
    </div>
</div>
@stop

@section('css')
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">-->
 
@stop

@section('js')
<script src="../vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function(){

var table = $('.localidades').DataTable({
            processing: true,
            responsive: true,
            autoWidth: false,
             stateSave: true,
            select: true,
            ajax: "{{ route('localidades.index') }}",
            columns: [
                {data: 'id'},
                {data: 'Nom_Loc'},
                {data: 'regiones.Nom_Reg'},
                {data: 'created_at'},
                {data: 'updated_at'},
                {data: 'action_loc'}
            ],
            "language": {
                "lengthMenu": "Numero _MENU_ registros por pagina",
                "zeroRecords": "Tabla Vacia No hay datos",
                "info": "pagina _PAGE_ de _PAGES_",
                "infoEmpty": "No existe ningun dato con esa busqueda",
                "infoFiltered": "(filtrado de _MAX_ total registros)",
                "search": "Buscador",
                "paginate":{
                    "next":"siguiente",
                    "previous":"anterior"
                }
            },
    });

/* abrir modal de nuevo registro*/
        $('#FormLoc').click(function(){
            $('#action').val('Add');//input para diferenciar con editar
            $('#form_result').html('');//el span donde aparece error
            $('#ModalAgregar').modal('show');
            $('.modal-title').text('Registrando Nueva Localidad');
            $('#Id_Reg').select2({
                dropdownParent: $('#ModalAgregar'),
                width:'100%'//en caso de no funcionar, puedes agregar el tama??o directamente en el input
            });
        });
/* peticion de guardar o editar el registro */
        $('#form_loc').on('submit', function(event){
                event.preventDefault(); 
                var action_url = '';
                if($('#action').val() == 'Add'){
                    $('#btnGuardarLoc').val('Guardando');//cambia valor de boton guardar
                    $('#action').val('');//input para diferenciar de editar
                    action_url = "{{ route('localidades.store') }}";
                }
        
                if($('#action').val() == 'Edit'){
                    $('#action').val('');//input para diferencia de guardar
                    $('#btnGuardarLoc').val('Actualizando');//cambia el texto de boton editar
                    action_url = "{{ route('localidades.update') }}";
                }
        
                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: action_url,
                    data:$(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        var html = '';
                        if(data.errors){
                            Swal.fire({
                                icon: 'error',
                                title: 'FALTO LLENAR ALGUN CAMPO',
                                text: 'LLENA TODOS LOS CAMPOS!',
                                
                            })
                            html = '<div class="alert alert-danger">';
                            for(var count = 0; count < data.errors.length; count++){
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if(data.success){
                            var tipo = data.success;
                            Swal.fire({
                                    position: 'Center',
                                    icon: 'success',
                                    title: 'La Localidad ha sido ' +tipo +' correctamente',
                                    showConfirmButton: false,
                                    timer: 1500
                            })
                            $('#btnGuardarLoc').val('Guardar');
                            $('#form_loc')[0].reset();
                            $("#Id_Reg").change();
                            $('#ModalAgregar').modal('hide');
                            $('.localidades').DataTable().ajax.reload();
                        }
                        $('#form_result').html(html);//coloca en caso de vacio input
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
        });
/* peticion de abrir modal de eliminar un registro */
    var loc_id;
    var nombre_loc;
    $(document).on('click', '.el_loc', function(){
        loc_id = $(this).attr('id');
        nombre_loc = $(this).attr('localidad');
        $('#ModalEliminar').modal('show');
        $('.modal-title').text('??Desea Eliminar esta Localidad?');
        document.getElementById('loc_editar').innerHTML = nombre_loc;
    });

/*PETICION DE ELIMINAR UN REGISTRO*/
    $('#btnElLoc').click(function(){
        $.ajax({
            url:"localidades/destroy/"+loc_id,
            beforeSend:function(){
                $('#btnElLoc').text('Eliminando');
            },
            success:function(data)
            {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'La Localidad ha sido Borrado correctamente',
                    showConfirmButton: false,
                    timer: 2000
                })
                $('#btnElLoc').text('Eliminar');
                $('#ModalEliminar').modal('hide');
                $('.localidades').DataTable().ajax.reload();
            }
        })
    });
/*peticion de abrir vista editar y cargar los datos*/
     $(document).on('click', '.edit', function(event){
        event.preventDefault(); 
        var id = $(this).attr('id'); //alert(id);
        $('#hidden_id').val(id);
        $('#form_result').html('');//donde aparece el error de vacio
        $('#ModalAgregar').modal('show');
        $('.modal-title').text('????Estas Editando una Localidad????');
        $('#btnGuardarLoc').val('Actualizar');
        $.ajax({
            url :"localidades/edit/"+id+"/",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType:"json",
            success:function(data)
            {
                console.log('success: '+data);
                $('#Nom_Loc').val(data.result.Nom_Loc);
                $('#Id_Reg').val(data.result.Id_Reg).change();
                $('#hidden_id').val(id);//colocar valor del id oculto
                $('#action').val('Edit');//colocar que sera un edit
            },
            error: function(data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        })
    });


});
</script>
@stop