<!-- resources/views/about-us/show.blade.php -->

<h1>Detail položky</h1>

<p>ID: {{ $aboutUs->id }}</p>
<p>Foto: <img src="{{ $aboutUs->photo }}" alt="Photo"></p>
<p>Citát: {{ $aboutUs->quote }}</p>
