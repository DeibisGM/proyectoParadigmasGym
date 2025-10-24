<style>
    .body-viewer-wrapper {
        text-align: center;
    }
    .body-view {
        display: inline-block;
        vertical-align: top;
        margin: 0 20px;
    }
    .body-container {
        position: relative;
        width: 15vw;
        max-width: 250px;
        margin: auto;
        aspect-ratio: 414 / 847;
        display: inline-block;
    }

    .body-container > svg {
        width: 100%;
        height: auto;
    }

    .body-part {
        position: absolute;
        transition: all 0.3s ease-in-out;
        z-index: 10;
        pointer-events: none;
    }

    .body-part svg path {
        pointer-events: all;
    }

    .body-part:hover {
        filter: drop-shadow(0 0 10px rgba(255, 255, 0, 0.8));
    }

    .tooltip {
        position: fixed;
        background-color: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 100;
    }

    #abdominales {
        width: 18%;
        top: 38%;
        left: 41%;
    }

    #abductores {
        width: 25%;
        top: 55%;
        left: 37.5%;
    }

    #biceps {
        width: 62%;
        top: 31%;
        left: 19%;
    }

    #extensoresdedosfrontales {
        width: 74%;
        top: 39.5%;
        left: 13%;
    }

    #flexoresdedos {
        width: 69%;
        top: 43%;
        left: 15.45%;
    }

    #gemelos {
        width: 19%;
        top: 76%;
        left: 40.7%;
    }

    #hombrosfrontales {
        width: 55%;
        top: 26%;
        left: 22.5%;
    }

    #oblicuosfrontales {
        width: 32%;
        top: 39%;
        left: 34%;
    }

    #pectoralmayor {
        width: 38%;
        top: 27.2%;
        left: 31%;
    }

    #quadriceps {
        width: 30%;
        top: 56%;
        left: 35%;
    }

    #serratoanterior {
        width: 33%;
        top: 35.5%;
        left: 33.4%;
    }

    #tibialanterior {
        width: 28%;
        top: 77%;
        left: 36.2%;
    }

    #trapeciofrontal {
        width: 28%;
        top: 21.3%;
        left: 35.8%;
    }

    #gluteo {
        width: 28%;
        top: 50%;
        left: 36%;
    }

    #Braquiorradialtrasero {
        width: 15%;
        top: 42%;
        left: 18%;
    }

    #deltoidetrasero {
        width: 40%;
        top: 26%;
        left: 30%;
    }

    #dorsalancho {
        width: 30%;
        top: 35%;
        left: 35%;
    }

    #extensordedos {
        width: 15%;
        top: 45%;
        left: 18%;
    }

    #gemelostrasero {
        width: 18%;
        top: 75%;
        left: 41%;
    }

    #Infraespinoso {
        width: 25%;
        top: 30%;
        left: 37.5%;
    }

    #Isquiotibiales {
        width: 25%;
        top: 60%;
        left: 37.5%;
    }

    #oblicuoexternotrasero {
        width: 15%;
        top: 42%;
        left: 67%;
    }

    #trapeciotrasero {
        width: 30%;
        top: 22%;
        left: 35%;
    }

    #tricepstraseros {
        width: 15%;
        top: 35%;
        left: 18%;
    }

</style>

<section>
<div class="body-viewer-wrapper">
    <div class="body-view">
    <h3>Vista Frontal</h3>
    <div class="body-container">
        <div class="tooltip"></div>
        <?php echo file_get_contents('view/bodyParts/Cuerpo.svg'); ?>
        <div id="abdominales" class="body-part" data-name="Abdominales">
            <?php echo file_get_contents('view/bodyParts/frontal/abdominales.svg'); ?>
        </div>
        <div id="abductores" class="body-part" data-name="Abductores">
            <?php echo file_get_contents('view/bodyParts/frontal/abductores.svg'); ?>
        </div>
        <div id="biceps" class="body-part" data-name="Biceps">
            <?php echo file_get_contents('view/bodyParts/frontal/biceps.svg'); ?>
        </div>
        <div id="extensoresdedosfrontales" class="body-part" data-name="Extensores de los dedos">
            <?php echo file_get_contents('view/bodyParts/frontal/extensoresdedosfrontales.svg'); ?>
        </div>
        <div id="flexoresdedos" class="body-part" data-name="Flexores de los dedos">
            <?php echo file_get_contents('view/bodyParts/frontal/flexoresdedos.svg'); ?>
        </div>
        <div id="gemelos" class="body-part" data-name="Gemelos">
            <?php echo file_get_contents('view/bodyParts/frontal/gemelos.svg'); ?>
        </div>
        <div id="hombrosfrontales" class="body-part" data-name="Hombros">
            <?php echo file_get_contents('view/bodyParts/frontal/hombrosfrontales.svg'); ?>
        </div>
        <div id="oblicuosfrontales" class="body-part" data-name="Oblicuos">
            <?php echo file_get_contents('view/bodyParts/frontal/oblicuosfrontales.svg'); ?>
        </div>
        <div id="pectoralmayor" class="body-part" data-name="Pectoral">
            <?php echo file_get_contents('view/bodyParts/frontal/pectoralmayor.svg'); ?>
        </div>
        <div id="quadriceps" class="body-part" data-name="Quadriceps">
            <?php echo file_get_contents('view/bodyParts/frontal/quadriceps.svg'); ?>
        </div>
        <div id="serratoanterior" class="body-part" data-name="Serrato Anterior">
            <?php echo file_get_contents('view/bodyParts/frontal/serratoanterior.svg'); ?>
        </div>
        <div id="tibialanterior" class="body-part" data-name="Tibial Anterior">
            <?php echo file_get_contents('view/bodyParts/frontal/tibialanterior.svg'); ?>
        </div>
        <div id="trapeciofrontal" class="body-part" data-name="Trapecio">
            <?php echo file_get_contents('view/bodyParts/frontal/trapeciofrontal.svg'); ?>
        </div>
    </div>
    </div>
    <div class="body-view">
    <h3>Vista Trasera</h3>
    <div class="body-container">
        <div class="tooltip"></div>
        <?php echo file_get_contents('view/bodyParts/Cuerpo.svg'); ?>
        <div id="gluteo" class="body-part" data-name="Gluteos">
            <?php echo file_get_contents('view/bodyParts/trasera/gluteo.svg'); ?>
        </div>
        <div id="Braquiorradialtrasero" class="body-part" data-name="Braquiorradial">
            <?php echo file_get_contents('view/bodyParts/trasera/Braquiorradialtrasero.svg'); ?>
        </div>
        <div id="deltoidetrasero" class="body-part" data-name="Deltoides">
            <?php echo file_get_contents('view/bodyParts/trasera/deltoidetrasero.svg'); ?>
        </div>
        <div id="dorsalancho" class="body-part" data-name="Dorsal Ancho">
            <?php echo file_get_contents('view/bodyParts/trasera/dorsalancho.svg'); ?>
        </div>
        <div id="extensordedos" class="body-part" data-name="Extensor de los dedos">
            <?php echo file_get_contents('view/bodyParts/trasera/extensordedos.svg'); ?>
        </div>
        <div id="gemelostrasero" class="body-part" data-name="Gemelos">
            <?php echo file_get_contents('view/bodyParts/trasera/gemelostrasero.svg'); ?>
        </div>
        <div id="Infraespinoso" class="body-part" data-name="Infraespinoso">
            <?php echo file_get_contents('view/bodyParts/trasera/Infraespinoso.svg'); ?>
        </div>
        <div id="Isquiotibiales" class="body-part" data-name="Isquiotibiales">
            <?php echo file_get_contents('view/bodyParts/trasera/Isquiotibiales.svg'); ?>
        </div>
        <div id="oblicuoexternotrasero" class="body-part" data-name="Oblicuo Externo">
            <?php echo file_get_contents('view/bodyParts/trasera/oblicuoexternotrasero.svg'); ?>
        </div>
        <div id="trapeciotrasero" class="body-part" data-name="Trapecio">
            <?php echo file_get_contents('view/bodyParts/trasera/trapeciotrasero.svg'); ?>
        </div>
        <div id="tricepstraseros" class="body-part" data-name="Triceps">
            <?php echo file_get_contents('view/bodyParts/trasera/tricepstraseros.svg'); ?>
        </div>
    </div>
    </div>
</div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bodyParts = document.querySelectorAll('.body-part');
        const tooltip = document.querySelector('.tooltip');

        bodyParts.forEach(part => {
            const paths = part.querySelectorAll('path');
            paths.forEach(path => {
                path.addEventListener('mouseover', function (e) {
                    part.classList.add('highlight');
                    const name = part.dataset.name;
                    tooltip.textContent = name;
                    tooltip.style.opacity = '1';
                });

                path.addEventListener('mouseout', function () {
                    part.classList.remove('highlight');
                    tooltip.style.opacity = '0';
                });

                path.addEventListener('mousemove', function (e) {
                    const x = e.clientX;
                    const y = e.clientY;
                    tooltip.style.left = (x + 10) + 'px';
                    tooltip.style.top = (y + 10) + 'px';
                });
            });
        });
    });
</script>