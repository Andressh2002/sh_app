<?php
    include '../src/components/login/access.php';
    $pageTitle = "Manual";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
?>

<div class="row my-3 p-4 mx-0">
    <div class="d-flex align-items-center gap-2 mb-4 px-0">
        <h4 class="mb-0">Manual de usuario</h4>
        <i class="bi bi-book-fill fs-4 d-flex align-self-center"></i>
    </div>

    <div class="d-block d-md-flex gap-2 p-0">
        <div class="col-12 col-md-4 order-1 order-md-2 p-0 d-none d-md-block">
            <div class="card mb-4 sticky-top sticky-col" style="background-color: #f9fafb;">
                <div class="card-body">
                    <h5 class="card-title">Tabla de contenido</h5>
                    <p class="card-text">Posibles preguntas que tengas con respecto a esta tienda</p>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">Inicio</a>
                        <a href="#funcionamiento" class="list-group-item list-group-item-action">¿Cómo funciona esta aplicación?</a>
                        <a href="#pedir" class="list-group-item list-group-item-action">¿Cómo realizo un pedido?</a>
                        <a href="#usuario" class="list-group-item list-group-item-action">¿Cómo creo un usuario?</a>
                        <a href="#olvidar" class="list-group-item list-group-item-action">¿Qué pasa si se me olvida la contraseña o nombre de usuario?</a>
                        <a href="#ver" class="list-group-item list-group-item-action">¿Cómo veo mis pedidos?</a>
                        <a href="#pagar" class="list-group-item list-group-item-action">¿Cómo pago mi pedido?</a>
                        <a href="#recomendar" class="list-group-item list-group-item-action">¿Puedo recomendar otros productos?</a>
                        <a href="#nuevos" class="list-group-item list-group-item-action">¿Llegan nuevos productos?</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 order-2 order-md-1 p-0">
            <section class="px-0 mb-4" id="funcionamiento">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Cómo funciona esta aplicación?</h5>
                        <p class="card-text">
                            Esta aplicación o tienda virtual funciona para que usted realice pedidos. Luego de recibir tu pedido, nosotros lo hacemos y se lo hacemos llegar a usted.
                            <br>
                            Actualmente solo hacemos entregas en Bagaces, Guanacaste. Si es de Cañas o Liberia se puede negociar la entrega, pero ya a otros lados no hacemos entregas.
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto w-100 h-auto w-100 h-auto" src="../src/img/manual/img_tienda.png" alt="...">
                        </div>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="pedir">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Cómo hago un pedido?</h5>
                        <p class="card-text">
                            En la aplicación tienes dos formas de hacer pedidos. Una con un usuario y otra si un usuario.
                            <br>
                            Pero antes de ver eso, usted tiene que escoger un producto de la tienda. Para ello navegamos por la tienda viendo los productos.
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_productos.png" alt="...">
                        </div>
                        <p class="card-text">
                            Una vez que ya escogido un producto, te debería mostrar algo similar a esto...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_pedirProducto.png" alt="...">
                        </div>
                        <p class="card-text">
                            Aquí tu puedes seleccionar tanto la cantidad de unidades que quieras pedir como el color del mismo producto. En pocas palabras, usted puede pedir tantos a tal color.
                            <br>
                            Ahora la parte importante, en el cuadro rojo en donde dice "Agregar a pedidos", puedes realizar tu pedido, solo si tienes un usuario ya creado. En caso de no tener uno ya creado, al dar clic en el botón (el que dice "Reservar") te saldrá un aviso que "¿si quieres continuar?", si le das clic en "si", te mostrará un formulario como este...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_pedirSinUsuario.png" alt="...">
                        </div>
                        <p class="card-text">
                            Completas el formulario y haces clic en el botón en donde dice "Realizar pedido" y listo.
                            <br>
                            Pero ojo, a nosotros nos llega el pedido, pero si lo realizas sin usuario, no puedes verlo ni cancelarlo. Ahora si quieres ver tus pedidos, entonces es recomendable que crees un usuario e inicies sesión.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="usuario">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Cómo hago un usuario?</h5>
                        <p class="card-text">
                            Es muy sencillo la verdad, te diriges en donde dice "Iniciar sesión" al final de la barra de navegación y te saldrá esta pantalla...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_login.png" alt="...">
                        </div>
                        <p class="card-text">
                            En caso de ya tener un usuario creado, solo digite los datos y le haces clic en el botón de "Ingresar", pero...
                            <br>
                            Si no tienes un usuario, entonces haces clic en donde dice "¡Registrala!" (es el enlace rojo), y te saldrá algo como esto...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_crearUsuario.png" alt="...">
                        </div>
                        <p class="card-text">
                            Un formulario que debes completar para crear el usuario.
                            <br>
                            Una vez ya completado, le haces clic en "Registrar" y después vuelves a la página de iniciar sesión de antes, pero ahora si puedes agregar tus credenciales e iniciar sesión con tu usuario creado.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="olvidar">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Qué pasa si se me olvida la contraseña o nombre de usuario?</h5>
                        <p class="card-text">
                            En caso que te pase eso, tienes que contactarnos para solucionar tu problema. O también usted tiene la opción de comunicarse por correo, puede escribir a sh.app2024@gmail.com.
                            <br>
                            Nosotros le daremos una nueva contraseña, también puedes cambiar la contraseña y otros datos aquí...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_editarUsuario.png" alt="...">
                        </div>
                        <p class="card-text">
                            En la sección de "Ver usuario", todo dato es editable aquí, incluso tu nombre de usuario. Solo si, no olvides la contraseña.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="ver">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Cómo veo mis pedidos?</h5>
                        <p class="card-text">
                            En la sección de "Pedidos", esta opción solo se ve si usted a iniciado sesión.
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_verPedidos.png" alt="...">
                        </div>
                        <p class="card-text">
                            Aquí usted puede ver sus pedidos en incluso puede quitarlos.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="pagar">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Cómo pago mi pedido?</h5>
                        <p class="card-text">
                            En la tienda virtual como tal no tiene métodos de pago, usted paga sus pedidos hasta cuando nosotros llegemos a usted con su pedido.
                            <br>
                            Tiene dos opciones para pagarnos: Ya sea en efectivo, o realizando un SINPE a un número que nosotros vamos a indicarle en cuanto le entregemos su producto.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="recomendar">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Puedo recomendar otros productos?</h5>
                        <p class="card-text">
                            Si, si puedes recomendarnos que hagamos otros productos, usted puede hacerlo ya sea comentando alguna de nuestra publicaciones en la <a href="https://www.facebook.com/share/12GA8uQTnEW/" target="_blank">página de Facebook</a> o complentando un formulario de google forms aquí en este lado del menú de la tienda...
                        </p>
                        <div class="m-auto text-center mb-3">
                            <img class="m-auto h-100 2-auto w-100 h-auto" src="../src/img/manual/img_compartirIdeas.png" alt="..." style="max-width: 474px;">
                        </div>
                        <p class="card-text">
                            Ahora tome en cuenta lo siguiente, puede ser que algunas ideas que nos lleguen no queden exacatemente igual a lo que usted podría sugerirnos, pero tratamos de complacer a nuestros clientes hasta donde nosotros podamos y estamos muy encantado de escuchar sus ideas, sin embargo, si es probable que no queden igual, por ejemplo, si nos llegan pedidos bastante grandes en altura o anchura, no lo podríamos hacer de ese tamaño, sería a algo similiar a los productos que están actualmente en la tienda. O que nos podría tomar varias semanas de elaboración por la complejidad del mismo.
                        </p>
                    </div>
                </div>
            </section>

            <section class="px-0 mb-4" id="nuevos">
                <div class="card" style="background-color: #f9fafb;">
                    <div class="card-body">
                        <h5 class="card-title">¿Llegan nuevos productos?</h5>
                        <p class="card-text">
                            Si, si llegan nuevos productos, todos los días trabajamos en nuevos productos para agregarlos a la tienda, siempre anunciamos los nuevos productos en nuestra página de Facebook.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    const listItems = document.querySelectorAll('.list-group-item');

    listItems.forEach(item => {
        item.addEventListener('click', function() {
            listItems.forEach(link => link.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>