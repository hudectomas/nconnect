<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
</head>
<body>
    <h1>{{ $page->title }}</h1>
    <div>{!! $page->content !!}</div>
</body>
</html>
