<section id="why-us" class="why-us section">


      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Warum mich wählen?</h2>
        <p>Entdecken Sie, warum meine Dienstleistungen perfekt auf Ihre Bedürfnisse zugeschnitten sind.</p>
      </div><!-- End Section Title -->

    <div class="container">

        <!-- Karten und Inhalte -->
        <div class="row gy-4 mt-4">

            <!-- Karte 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item position-relative h-100">
                    <img src="assets/images/why-us-1.webp" class="card-img-top" alt="Individuelle Lösungen">
                    <h4>Individuelle Lösungen</h4>
                    <p>Ich entwickle maßgeschneiderte Weblösungen, die speziell auf Ihre Anforderungen zugeschnitten sind.</p>
                    <a href="{{ route('schedule.appointment') }}" class="stretched-link"></a>
                </div>
            </div>

            <!-- Karte 2 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-item position-relative h-100">
                    <img src="assets/images/why-us-2.webp" class="card-img-top" alt="Hochwertige Technologien">
                    <h4>Hochwertige Technologien</h4>
                    <p>Meine Expertise in modernen Frameworks wie Laravel, Bootstrap und mehr sorgt für erstklassige Ergebnisse.</p>
                    <a href="{{ route('schedule.appointment') }}" class="stretched-link"></a>
                </div>
            </div>

            <!-- Karte 3 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-item position-relative h-100">
                    <img src="assets/images/why-us-3.webp" class="card-img-top" alt="Zuverlässigkeit">
                    <h4>Zuverlässigkeit</h4>
                    <p>Mit einem starken Fokus auf Qualität und Pünktlichkeit bin ich Ihr verlässlicher Partner.</p>
                    <a href="{{ route('schedule.appointment') }}" class="stretched-link"></a>
                </div>
            </div>

        </div>

    </div>

</section>

<style>
/* Allgemeines Styling */
.service-item {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%; /* Einheitliche Höhe */
}

/* Bild Styling */
.service-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* Text Styling */
.service-item h4 {
    font-size: 1.25rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.service-item p {
    color: #666;
    font-size: 1rem;
    margin-bottom: 15px;
}

/* Hover-Effekte */
.service-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    border-bottom: 4px solid #28a745; /* Grüner Rahmen unten */
}

/* Stretched-Link */
.service-item .stretched-link {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
    pointer-events: auto;
}
</style>
