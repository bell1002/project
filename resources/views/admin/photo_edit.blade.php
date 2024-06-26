@extends('admin.layout.app')

@section('heading', 'Edit Photo')

@section('right_top_button')
<a href="{{ route('admin_photo_view') }}" class="btn btn-primary"><i class="fa fa-eye"></i>View All</a>

@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin_photo_update', $photo_data) }}" method="post" enctype="multipart/form-data">                       
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label">Existing Photo *</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$photo_data->photo) }}"  alt="" class="w_200">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Change Photo *</label>
                                    <div>
                                        <input type="file"  name="photo" >
                                    </div>
                                </div>                                
                                <div class="mb-4">
                                    <label class="form-label">Caption *</label>
                                    <textarea name="caption" class="form-control h_100" cols="30" rows="10">{{ $photo_data->caption }}</textarea>
                                </div>  
                                <div class="mb-4">
                                    <label class="form-label"></label>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection