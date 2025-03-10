@extends('includes.adminhead')
@section('middle')
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>



<div class="modal fade" id="addcategory" role="dialog">
    <div class="modal-dialog">

        <!--  task Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color:#33bcb8; color: #000000;">
                <p class="modal-title" style="color: #000000"><b>Enter Sector Detail Below</b></p>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ url('categories') }}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

                    <label>Sector Name:</label>
                    <input type="text" class="form-control" name="name" required="required" ><br>

                    <label>Description</label>
                    <textarea class="form-control" name="description">

                    </textarea>

                    <label>Background Image:</label>
                    <input type="file" class="form-control" name="pic" required="required" ><br>


                    <br>
                    <input type="submit" class="btn btn-primary">
                </form>
            </div>
            <div class="modal-footer"  style="background-color: #33bcb8; color: #ffffff;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="editcategory" role="dialog">
    <div class="modal-dialog">

        <!--  task Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="background-color:#33bcb8; color: #000000;">
                <p class="modal-title" style="color: #000000"><b>Enter Sector Detail Below</b></p>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ url('editcategory') }}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                    <input type="hidden" name="id" id="id">

                    <label>Sector Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required="required" ><br>




                    <label>Description</label>
                    <textarea class="form-control" id="description" name="description"><br>

                    </textarea>
<br>

                    <div id="section_image">

                    </div><br>
                    <label>Background Image:</label>
                    <input type="file" class="form-control" name="pic" ><br>

                    <br>
                    <input type="submit" class="btn btn-primary">
                </form>
            </div>
            <div class="modal-footer"  style="background-color: #33bcb8; color: #ffffff;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>




<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Sectors</h3>
                <button class=" btn btn-primary btn-sm"  data-toggle="modal" data-target="#addcategory" style="margin-left: 25em; float: right; height: 30px;"><i class="fa fa-plus fa-fw"></i>Add Sector</button>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-bordered" style="border: 1px solid #000000;">
                    <?php $count=1; ?>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="width: 40%">Action</th>
                    </tr>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <div class="col-md-2">
                                <button class="edit btn btn-primary btn-sm" value="{{ $category->id }}"><i class="glyphicon glyphicon-pencil"></i>Edit</button>
                            </div>
                            <div class="col-md-2" style="margin-left: 13px;">
                                <form action="{{ url('categories',$category->id) }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" onclick="return confirm('Are you sure to want to delete this?');" name="add_btn" value="Delete" class="btn btn-danger btn-sm" ><i class="glyphicon glyphicon-remove"></i>Delete</button>

                                </form>
                            </div>

                            </form>
                        </td>
                    </tr>
                    <?php $count=$count+1; ?>
                    @endforeach

                </table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <ul class="pagination pagination-sm no-margin pull-right">

                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.edit').on('click',function(){

            var id=$(this).val();

            $.get('{{ url("categories") }}/'+id+'/edit',function(response){
                $('#section_image').empty();
                $.each(response,function(key,value){
                    $('#name').val(value.name);
                    var id=value.id;
                    $('#id').val(id);
                    $('#description').val(value.description);
                    $('#section_image').append('<img style="width: 150px; height: 150px;" src="public/uploads/sectors'+'/'+ value.bg_pic +'">');
                });

                $('#editcategory').modal('show');
            });
        });
    });
</script>

@endsection