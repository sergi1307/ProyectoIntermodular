<!doctype html><html><body>
<h1>Editar</h1>
<form method="POST" action="{{ route('favorites.update',$favorite->id_favorite) }}">
@csrf @method('PUT')
<input name="id_user" value="{{ old('id_user',$favorite->id_user) }}">
<input name="id_product" value="{{ old('id_product',$favorite->id_product) }}">
<button>Actualizar</button>
</form>
<a href="{{ route('favorites.index') }}">Volver</a>
</body></html>

