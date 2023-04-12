<style>
    .logger-container {
        overflow:hidden;
        border-radius:25px;
        border:10px solid #d3d7d9;
    }
    .logger-message {
        box-sizing:border-box;
        overflow:auto;
        width:100%;
        margin:0;
        border:0;
        border-top:1px solid rgba(0,0,0,0.1);
        padding:7px;
    }
</style>
<div class="logger-container">
    @foreach($messages as $v)
        <pre class="logger-message" style="background-color:{{$v['color']}};">[{{$v['time']}}] {{$v['message']}}</pre>
    @endforeach
</div>

