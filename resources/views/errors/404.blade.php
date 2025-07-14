<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 - Not Found</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Toastr & Font -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <style>
    body {
      margin: 0;
      padding: 0;
    }

    .error-container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f8f9fa;
      padding: 1rem;
    }

    .error-content {
      text-align: center;
    }

    .error-content h1 {
      font-size: 6rem;
      font-weight: bold;
      margin-bottom: 1rem;
    }

    .error-content p {
      font-size: 1.5rem;
      margin-bottom: 2rem;
    }

    .lottie-animation {
      max-width: 400px;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>
  <div class="error-container">
    <div class="lottie-animation"></div>
    <div class="error-content">
      <h1>404</h1>
      <p>Oops! Halaman tidak ditemukan :(</p>
      <a href="{{ url('/home') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
      </a>
    </div>
  </div>

  <!-- Lottie -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
  <script>
    const animation = lottie.loadAnimation({
      container: document.querySelector('.lottie-animation'),
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: 'https://lottie.host/d987597c-7676-4424-8817-7fca6dc1a33e/BVrFXsaeui.json'
    });
  </script>
</body>
</html>
