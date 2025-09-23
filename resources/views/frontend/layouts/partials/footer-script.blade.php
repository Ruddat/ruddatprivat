  {{-- resources\views\frontend\layouts\partials\footer-script.blade.php --}}

  <!-- Vendor JS Files -->
  <script src="{{ asset("assets/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
  <script src="{{ asset("assets/vendor/php-email-form/validate.js") }}"></script>
  <script src="{{ asset("assets/vendor/aos/aos.js") }}"></script>
  <script src="{{ asset("assets/vendor/glightbox/js/glightbox.min.js") }}"></script>
  <script src="{{ asset("assets/vendor/purecounter/purecounter_vanilla.js") }}"></script>
  <script src="{{ asset("assets/vendor/imagesloaded/imagesloaded.pkgd.min.js") }}"></script>
  <script src="{{ asset("assets/vendor/isotope-layout/isotope.pkgd.min.js") }}"></script>
  <script src="{{ asset("assets/vendor/swiper/swiper-bundle.min.js") }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset("assets/js/main.js") }}"></script>

  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

  <script>
      var AOS;
      if (AOS) {
          AOS.init();
          document.querySelector('body').setAttribute('data-aos-enabled', true);
      }

      $(function() {
          // refresh animations positions after page was rendered.
          if (AOS) {
              AOS.refresh();
          }
      });
  </script>
  <script>
      document.querySelectorAll('a[href^="/#"]').forEach(anchor => {
          anchor.addEventListener('click', function(e) {
              e.preventDefault();
              const target = this.getAttribute('href').split('#')[1];
              window.location.href = `/${target}`;
          });
      });
  </script>
