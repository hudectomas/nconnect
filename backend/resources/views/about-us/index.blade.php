<!-- resources/views/about-us/index.blade.php -->

<h1>O nás</h1>

<a href="{{ route('about-us.create') }}">Vytvoriť novú položku</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Foto</th>
            <th>Citát</th>
            <th>Akcie</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($aboutUs as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td><img src="{{ $item->photo }}" alt="Photo"></td>
            <td>{{ $item->quote }}</td>
            <td>
                <a href="{{ route('about-us.edit', $item->id) }}">Upraviť</a>
                <!-- Implementovať možnosť odstránenia -->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
