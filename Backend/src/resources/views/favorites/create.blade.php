<!doctype html><html><body>
<h1>Crear</h1>
<form method="POST" action="{{ route('favorites.store') }}">
@csrf
<input name="id_user" placeholder="id_user">
<input name="id_product" placeholder="id_product">
<button>Guardar</button>
</form>
<a href="{{ route('favorites.index') }}">Volver</a>
</body></html>

