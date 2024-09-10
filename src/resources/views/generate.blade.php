<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHQR Code Generator</title>
    <!-- Include any required CSS or JavaScript here -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>KHQR Code Generator</h1>
        <form id="khqrForm" method="POST" action="{{ route('generate.khqr') }}">
            @csrf <!-- Laravel CSRF Protection -->
            <div class="mb-3">
                <label for="amount" class="form-label">Enter Amount (USD):</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate QR Code</button>
        </form>

        <!-- QR Code Display Section -->
        @if(session('qr'))
        <div class="mt-5">
            <h2>Generated QR Code</h2>
            <img src="data:image/png;base64,{{ session('qr') }}" alt="KHQR Code">
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
