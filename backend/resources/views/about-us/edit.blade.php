<!-- resources/views/about-us/edit.blade.php -->

<h1>Upraviť položku</h1>

<form action="{{ route('about-us.update', $aboutUs->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <label for="photo">Foto:</label>
        <input type="text" id="photo" name="photo" value="{{ $aboutUs->photo }}" required>
    </div>
    <div>
        <label for="quote">Citát:</label>
        <textarea id="quote" name="quote" required>{{ $aboutUs->quote }}</textarea>
    </div>
    <button type="submit">Uložiť zmeny</button>
</form>
