<!DOCTYPE html>
<html>
<head>
    <title>Inventory Spare Part</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <nav class="mb-4">
            <a href="{{ route('spare-parts.index') }}" class="btn btn-primary">Spare Parts</a>
            <a href="{{ route('stock-in.index') }}" class="btn btn-success">Stok Masuk</a>
            <a href="{{ route('stock-out.index') }}" class="btn btn-danger">Stok Keluar</a>
        </nav>
        @yield('content')
    </div>
</body>
</html>
