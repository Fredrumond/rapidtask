<div class="panel-header">
    <div class="panel-title">
        <h4>{{ $title }}</h4>
    </div>
    <div class="panel-action">
        @isset($actions)
            @foreach($actions as $action)
                <a class="btn btn-{{ $action->class}}" href="{{ route($action->route) }}">{{ $action->title }}</a>
            @endforeach
        @endisset
        
    </div>
</div>