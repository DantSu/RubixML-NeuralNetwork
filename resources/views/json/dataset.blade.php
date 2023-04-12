[@foreach($data as $k => $v){{
    $k !== 0 ? ',' : ''}}
  {"x":{{$v['x']}},"y":{{$v['y']}},"v":"{{$v['v']}}"}@endforeach
]
