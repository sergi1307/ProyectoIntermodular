<!doctype html><html><body>
<h1>Favoritos</h1>
<a href="{{ route('favorites.create') }}">Crear</a>
<ul>
@foreach($favorites as $f)
<li>
    {{ $f->id_favorite }} —
    {{ $f->user->name ?? '' }} —
    {{ $f->product->name ?? '' }}
    <a href="{{ route('favorites.show',$f->id_favorite) }}">Ver</a>
    <a href="{{ route('favorites.edit',$f->id_favorite) }}">Editar</a>
    <form action="{{ route('favorites.destroy',$f->id_favorite) }}" method="POST" style="display:inline">
        @csrf @method('DELETE') <button>Eliminar</button>
    </form>
</li>
@endforeach
</ul>
</body></html>

