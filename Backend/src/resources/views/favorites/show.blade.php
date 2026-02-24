<!doctype html><html><body>
<h1>Detalle</h1>
<p>ID: {{ $favorite->id_favorite }}</p>
<p>Usuario: {{ $favorite->user->name ?? '' }}</p>
<p>Producto: {{ $favorite->product->name ?? '' }}</p>
<a href="{{ route('favorites.index') }}">Volver</a>
</body></html>

