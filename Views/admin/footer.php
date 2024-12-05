<style>
    footer{
        background: #f9e612;
        width: 100%;    
        padding: 1px;    
        position: absolute;
        bottom:0;       
    }
    footer p{           
        text-align: center;        
    }
    #contact-icons i.fas, #contact-icons i.fab {
        color: #000; 
        font-size: 20px;
        margin-right: 10px; 
    }
    #contact-icons a {
        color: #000;
        text-decoration: none;
    }
    #contact-icons a:hover {
        color: #007bff; 
    }
    .modal-body p {
        line-height: 1.8; 
    }
    .text-muted {
        font-size: 14px;
        margin-top: 15px;
    }
</style>
<br/><br/>
<!-- -------------------------------- -->
<footer class="text-center">  
    Creado por estudiantes de
    <?php if ($_SESSION['id_rol'] === 1 ) { ?>
        <a href="#" class="text-dark" data-bs-toggle="modal" data-bs-target="#creatorsModal">ISFT Angaco</a> 2023 
    <?php }else{?>  ISFT Angaco 2023  <?php }?>    
</footer>
<!---------- Creators Modal ----------->
<div class="modal fade" id="creatorsModal" tabindex="-1" aria-labelledby="creatorsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">                
                <h5 class="modal-title" id="creatorsModalLabel">
                    Creado por estudiantes de ISFT Angaco <i class="fas fa-user pr-2" title="Olmos, Perez, Rodriguez"></i>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div id="contact-icons" class="text-left">
                    <p>
                        <i class="fas fa-envelope"></i>isftangaco@gmail.com<br>
                        <a href="https://instagram.com/angacoisft" target="_blank">
                            <i class="fab fa-instagram"></i>@angacoisft
                        </a><br>
                        <a href="https://maps.app.goo.gl/U4cWXeZn1LFru3X79" target="_blank">
                            <i class="fas fa-map-marker-alt"></i>Colegio Secundario Cacique Angaco - Angaco, San Juan
                        </a>
                    </p>
                </div>
                <div class="mt-3 text-center">
                    <img src="../../img/logo_angaco.png" alt="Contacto ISFT Angaco" class="img-fluid rounded" style="height: 50px;">
                </div>
                <p class="text-muted text-left py-0 my-0">
                    ¡Contáctanos para más información sobre nuestros proyectos y programas educativos!
                </p>
            </div>   
            <div class="modal-footer py-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- ----------- -->
</body>
</html>