@extends('admin.layout.app')

@section('heading', 'Update Page About')


@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin_page_about_update') }}" method="post">
                        
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="mb-4">
                                    <label class="form-label">Heading *</label>
                                    <input type="text" class="form-control" name="about_heading" value="{{ $page_data->about_heading ?? '' }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Content *</label>
                                    <textarea name="about_content" class="form-control h_100 snote" cols="30" rows="10">{{ $page_data->about_content ?? ''}}</textarea>
                                </div>  
                                <div class="mb-4">
                                    <label class="form-label">Status *</label>
                                    <select name="about_status" class="form-control">
                                        @if ($page_data && isset($page_data->about_status))
                                            <option value="1" {{ $page_data->about_status == 1 ? 'selected' : '' }}>Show</option>
                                            <option value="0" {{ $page_data->about_status == 0 ? 'selected' : '' }}>Hide</option>
                                        @else
                                            <option value="1">Show</option>
                                            <option value="0">Hide</option>
                                        @endif
 
                                    </select>
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