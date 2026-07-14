<?php  
$matched = true; // Change to false for no match
$phoneNumber = '0393249845';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Success - ID Found | iRecover</title>
  <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-color: #a83232;
      --primary-dark: #7a2626;
      --secondary-color: #121212;
      --text-color: #ffffff;
      --footer-link: #8a8989;
      --footer-link-hover: #a54e4e;
      --card-bg: rgba(40, 40, 40, 0.9);
      --border-color: #333333;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: url('img/bg.jpg') no-repeat center center fixed;
      background-size: cover;
      color: var(--text-color);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      margin: 0;
    }

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: -1;
    }

    .container {
      max-width: 90%;
      width: 100%;
      margin: clamp(2rem, 5vw, 5rem) auto;
      background: var(--card-bg);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: clamp(1rem, 5vw, 2rem);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      border: 1px solid var(--border-color);
      text-align: center;
    }

    .alert {
      font-size: clamp(0.9rem, 2.5vw, 1rem);
      border-radius: 10px;
      border: none;
      margin-bottom: clamp(10px, 2.5vw, 20px);
    }

    .alert-success {
      background-color: var(--primary-color);
      color: var(--text-color);
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .alert-danger {
      background-color: #b33a3a;
      color: var(--text-color);
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-close {
      filter: brightness(0) invert(1);
    }

    .btn-phone {
      background-color: var(--primary-color);
      color: var(--text-color);
      border: none;
      border-radius: 6px;
      padding: clamp(8px, 2.5vw, 12px) clamp(12px, 3vw, 16px);
      font-size: clamp(0.85rem, 2.2vw, 0.875rem);
      font-weight: 500;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
    }

    .btn-phone:hover,
    .btn-phone:focus {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      color: var(--text-color);
    }

    h3, h4, p {
      margin-bottom: clamp(10px, 2.5vw, 15px);
      font-size: clamp(1rem, 3vw, 1.2rem);
    }

    h3 {
      font-size: clamp(1.5rem, 4vw, 1.8rem);
    }

    h4 {
      font-size: clamp(1.2rem, 3.5vw, 1.4rem);
    }

    .lead {
      font-weight: 600;
      color: var(--primary-color);
      font-size: clamp(1.1rem, 3vw, 1.3rem);
    }

    #countdown {
      font-weight: bold;
      color: var(--primary-color);
    }

    .footer {
      background-color: #1a1a1a;
      text-align: center;
      padding: clamp(0.8rem, 2vw, 1rem);
      margin-top: auto;
      font-size: clamp(0.8rem, 2vw, 0.9rem);
      width: 100%;
    }

    .footer a {
      color: var(--footer-link);
      text-decoration: none;
    }

    .footer a:hover {
      color: var(--footer-link-hover);
    }

    /* Media Queries for Smaller Devices */
    @media (max-width: 576px) {
      .container {
        margin: 1.5rem auto;
        padding: 1rem;
      }

      .btn-phone {
        width: 100%;
        justify-content: center;
      }

      .alert {
        font-size: 0.9rem;
      }
    }

    /* Background Image Optimization */
    body {
      background-image: url('img/bg.jpg');
      background-image: image-set(
        url('img/bg-lowres.jpg') 1x,
        url('img/bg.jpg') 2x
      );
      background-size: cover;
      background-attachment: fixed;
    }
  </style>
</head>
<body>

<div class="container" role="main">
  <?php if ($matched): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-lg rounded" role="alert">
      <h4 class="alert-heading">Success!</h4>
      <p>We’ve found your Document.</p>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <h3>Matched! 🎯</h3>
    <p>Your ID has been successfully verified. If you have questions or need help, reach out using the number below.</p>

    <h4>For assistance, call:</h4>
    <p class="lead"><?php echo htmlspecialchars($phoneNumber); ?></p>
    <a href="tel:<?php echo htmlspecialchars($phoneNumber); ?>" class="btn-phone" aria-label="Call support"><i class="bi bi-telephone"></i> Call Now</a>

    <p class="mt-4">Redirecting to the home page in <span id="countdown">5</span> seconds...</p>

  <?php else: ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-lg rounded" role="alert">
      <h4 class="alert-heading">No match found.</h4>
      <p>We couldn’t verify your ID details.</p>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <p>Please try again or contact support.</p>
    <p class="mt-4">Redirecting to the home page in <span id="countdown">5</span> seconds...</p>
  <?php endif; ?>
</div>

<footer class="footer">
  © <?php echo date("Y"); ?> iRecover. Powered by 
  <a href="https://kakebe.tech/" target="_blank" rel="noopener noreferrer">Kakebe Technologies Limited</a>. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let count = 5;
  const countdown = document.getElementById("countdown");
  const interval = setInterval(() => {
    count--;
    countdown.textContent = count;
    if (count <= 0) {
      clearInterval(interval);
      window.location.href = "index.php";
    }
  }, 1000);
</script>

</body>
</html>