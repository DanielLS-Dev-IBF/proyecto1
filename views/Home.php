
<link rel="stylesheet" href="css/Home.css">
<?php include_once "TopNav.php"; ?>
<main>
    <!-- Sección principal con título y descripción -->
    <section class="container my-5 text-center" id="section_home1">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <h1 class="display-4">¿Listo para un sinfín</h1>
                <h1 class="display-4">de combinaciones?</h1>
                <p class="lead">Embárcate en busca de la combinación perfecta </p> 
                <p class="lead">con nuestra gran variedad de ingredientes,</p> 
                <p class="lead"> ¡crea tu poke bowl ideal!</p>
                <a class="btn-hover" href="?controller=Producto&action=carta">Que empiece la magia</a>
                <p class="mt-3">¿Ya tienes una cuenta? <a href="?controller=Usuario&action=login">Inicia sesión ahora</a></p>
            </div>
        </div>
    </section>

    <!-- Slick List con Bootstrap -->
    <section class="container mt-5 mb-5 text-center">
        <div class="autoplay">
            <!-- Elementos de la slick list -->
             <div class="slick-item">
                <div class="card h-100">
                    <h5 class="card-title">Combinaciones</h5>
                    <img src="/DAW2/Proyecto1/img/Refs/13609060-Poke-Bowl-mit-Garnelen-Edamame-und-Reis.jpg" class="card-img-top" alt="Combinaciones">
                </div>
            </div>
            <div class="slick-item">
                <div class="card h-100">
                    <h5 class="card-title">Ingredientes</h5>
                    <img src="/DAW2/Proyecto1/img/Refs/pokeflacocdm5-kIcH-U130390739046KgG-624x825@Diario Montanes.webp" class="card-img-top" alt="Ingredientes">
                </div>
            </div>
            <div class="slick-item">
                <div class="card h-100">
                    <h5 class="card-title">Platos</h5>
                    <img src="/DAW2/Proyecto1/img/Refs/Pokes-de-4-makis-scaled.webp" class="card-img-top" alt="Platos">
                </div>
            </div>
            <div class="slick-item">
                <div class="card h-100">
                    <h5 class="card-title">Establecimientos</h5>
                    <img src="/DAW2/Proyecto1/img/Refs/Poke-bar-paris-poke-bowl-sur-mesure-photo-nyctoparis.webp" class="card-img-top" alt="Establecimientos">
                </div>
            </div>
            <div class="slick-item">
                <div class="card h-100">
                    <h5 class="card-title">Reseñas</h5>
                    <img src="/DAW2/Proyecto1/img/Refs/von-oben-der-ernte-anonyme-weibliche-essen-koestliche-poke-gericht-mit-staebchen-beim-sitzen-am-holztisch-im-restaurant-ADSF59119.jpg" class="card-img-top" alt="Reseñas">
                </div>
            </div>
        </div>
    </section>

    <!-- Sección adicional de contenido -->
    <section class="container text-center my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <h1 class="display-4">Domina el arte de</h1>
                <h1 class="display-4">las combinaciones,</h1>
                <h1 class="display-4">disfruta el doble</h1>
                <p class="lead">Vuelve atrás en el tiempo y recuerda todo lo que </p> 
                <p>has aprendido, es hora de ponerlo en práctica</p>
                
                <!-- Botón antes de la imagen -->
                 <div>
                    <a class="btn-hover mb-3" href="">Que empiece la magia</a>
                 </div>
                
                <!-- Imagen debajo del botón -->
                 <div>
                    <img src="/DAW2/Proyecto1/img/Refs/2024-11-11_20_08_04-NVIDIA_GeForce_Overlay_DT-removebg-preview.png" 
                        alt="Imagen introducción a la página" 
                        class="img-fluid mt-3">
                 </div>
            </div>
        </div>
    </section>



    <section class="container text-center my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <h1 class="display-4">Acerca de nuestros</h1>
                <h1 class="display-4">ingredientes</h1>
                <p class="lead">Descubre más acerca de nuestros ingredientes y su procedencia</p>
                <a class="btn-hover" href="">Descubrir más</a>
            </div>
        </div>
    </section>
</main>
<?php include_once "Footer.php"; ?>
<!-- Importación de jQuery y Slick usando CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('.autoplay').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: false,
        arrows: true,
        nextArrow: `
          <button type="button" class="slick-prev">
            <img src="/DAW2/Proyecto1/img/Iconos/prev-arrow.svg" alt="Flecha izquierda">
          </button>`,
        prevArrow: `
          <button type="button" class="slick-next">
            <img src="/DAW2/Proyecto1/img/Iconos/next-arrow.svg" alt="Flecha derecha">
          </button>`,
        infinite: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
  });
</script>

