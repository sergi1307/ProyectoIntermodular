<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:vistas {recurso} {--campos=}', function () {
    $recurso = $this->argument('recurso');
    $camposOpt = $this->option('campos');
    $fields = [];
    if ($camposOpt) {
        $parts = array_filter(array_map('trim', explode(',', $camposOpt)));
        foreach ($parts as $p) {
            if ($p !== '') $fields[] = Str::snake($p);
        }
    }
    if (empty($fields)) $fields = ['name'];
    $singularStudly = Str::studly($recurso);
    $singular = Str::snake($recurso);
    $plural = Str::snake(Str::pluralStudly($recurso));
    $fs = new Filesystem();
    $dir = resource_path('views/'.$plural);
    if (!$fs->isDirectory($dir)) $fs->makeDirectory($dir, 0755, true);
    $index = '<!doctype html><html><body>
<h1>Listado de '.$singularStudly.'</h1>
<a href="{{ route(\''.$plural.'.create\') }}">Crear</a>
<ul>
@foreach($items as $'.$singular.')
<li>
    {{ $'.$singular.'->getKey() }}
    <a href="{{ route(\''.$plural.'.show\', $'.$singular.'->getKey()) }}">Ver</a>
    <a href="{{ route(\''.$plural.'.edit\', $'.$singular.'->getKey()) }}">Editar</a>
    <form action="{{ route(\''.$plural.'.destroy\', $'.$singular.'->getKey()) }}" method="POST" style="display:inline">
        @csrf @method(\'DELETE\') <button>Eliminar</button>
    </form>
</li>
@endforeach
</ul>
</body></html>
';
    $createInputs = '';
    foreach ($fields as $f) {
        $createInputs .= '<label>'.$f.': <input name="'.$f.'" value="{{ old(\''.$f.'\') }}"></label><br/>'."\n";
    }
    $create = '<!doctype html><html><body>
<h1>Crear '.$singularStudly.'</h1>
<form method="POST" action="{{ route(\''.$plural.'.store\') }}">
@csrf
'.$createInputs.'
<button>Guardar</button>
</form>
<a href="{{ route(\''.$plural.'.index\') }}">Volver</a>
</body></html>
';
    $show = '<!doctype html><html><body>
<h1>Detalle de '.$singularStudly.'</h1>
<pre>{!! print_r($'.$singular.'->toArray(), true) !!}</pre>
<a href="{{ route(\''.$plural.'.index\') }}">Volver</a>
</body></html>
';
    $editInputs = '';
    foreach ($fields as $f) {
        $editInputs .= '<label>'.$f.': <input name="'.$f.'" value="{{ old(\''.$f.'\', $'.$singular.'->'.$f.') }}"></label><br/>'."\n";
    }
    $edit = '<!doctype html><html><body>
<h1>Editar '.$singularStudly.'</h1>
<form method="POST" action="{{ route(\''.$plural.'.update\', $'.$singular.'->getKey()) }}">
@csrf @method(\'PUT\')
'.$editInputs.'
<button>Actualizar</button>
</form>
<a href="{{ route(\''.$plural.'.index\') }}">Volver</a>
</body></html>
';
    $fs->put($dir.'/index.blade.php', $index);
    $fs->put($dir.'/create.blade.php', $create);
    $fs->put($dir.'/show.blade.php', $show);
    $fs->put($dir.'/edit.blade.php', $edit);
    $this->info('Vistas generadas en resources/views/'.$plural);
})->purpose('Genera vistas Blade básicas para un recurso en español');
