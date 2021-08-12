<div class="form-group">
    <label>Title</label>
    <input type="text" name='title' value="{{old('title', $post->title??null)}}" class="form-control" />
    </div>
    <div class="form-group">
    <label>Content</label>
    <input type="text" name='content' value="{{old('content',$post->content??null)}}"  class="form-control"/>
    </div>

    <div class="form-group">
        <label>Thumbnail</label>
        <input type="file" name="thumbnail" class="form-control-file">
    </div>
    
    @if($errors->any())
    <div class="mt-2 mb-2">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endforeach
    </div>
@endif