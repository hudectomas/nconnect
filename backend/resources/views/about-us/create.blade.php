<!-- resources/views/about-us/create.blade.php -->

<h1>Vytvoriť novú položku</h1>

<form action="{{ route('about-us.store') }}" method="POST">
    @csrf
    <div>
        <label for="photo">Foto:</label>
        <input type="text" id="photo" name="photo" required>
    </div>
    <div>
        <label for="quote">Citát:</label>
        <textarea id="quote" name="quote" required></textarea>
    </div>
    <button type="submit">Vytvoriť</button>
</form>
