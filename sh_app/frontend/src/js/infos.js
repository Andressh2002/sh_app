function showInfo(page) {
    const title = 'Información';
    const icon = 'info';
    const buttonText = 'Cerrar';
    let text = '...';
    switch (page) {
        case 'dashboard':
            text = 'En esta parte se muestra los ingresos, pedidos de productos y otros indicadores generados a base de las ventas de la tienda. Puedes obtener información más detallada usando los filtros de búsqueda.';
            break;
        case 'productos':
            text = 'En esta parte se muestran los productos que has registrado en la tienda, puedes hacerlos visibles o no, y puedes modificarlos y borrarlos. También puedes ver cuantos se han pedido y vendido, incluso ver el número de estrellas que le han dado los clientes a los productos.';
            break;
        case 'categorías':
            text = 'En esta parte se muestran las categorías que has registrado, puedes modificarlos y borrarlos. Las categorías son para hacer distinguir el tipo de producto que venderás, es decir, asignar una familia a los productos.';
            break;
        case 'colores':
            text = 'En esta parte se muestran los colores que has registrado, puedes modificarlos y borrarlos. Los colores son para agregar paletas de colores a tus productos, esto es por si quieres agregar un producto con posibilidad de venderlo a otros colores.';
            break;
        case 'festividades':
            text = 'En esta parte se muestran las festividades que has registrado, puedes modificarlos y borrarlos. Las festividades son una clasificación especial para los productos a cuales vas a asignar este identificador, lo que hace esto es hacer visible los productos en un rango de tiempo determinado a cuales se los asignes. Como por ejemplo, un producto solamente disponible de tal fecha a tal fecha.';
            break;
        case 'pedidos':
            text = 'En esta parte se muestran los pedidos de los clientes, de donde son, que color pidieron, cuantos pidieron, cuanto deberán pagar, y también puedes ya establecerlos como pagados. !Cuidado¡, procura primero entregar el producto y ya haber recibido el pago para hacer clic al botón de "pagar". Para saber que pedidos tienes, usa los filtros para más detalles, en especial en donde dice "¿está pagado?, ahí te indica mejor cuales aún no se han pagado ni entregado.';
            break;
        case 'usuarios':
            text = 'En esta parte se muestran los usuarios que has registrado y de los clientes quienes han creado un usuario, puedes modificarlos y borrarlos. ¡Cuidado!, Si vas a eliminar un cliente, entonces ten en cuenta que no es algo que se tome a la ligera, sería en casos en el que el cliente cometa varias faltas como una conducta inapropiada en los comentarios o revelar información personal o algo de mayor gravedad. Y modificar en caso de que un cliente haya olvidado la contraseña o el nombre de usuario. Evita reclamos posteriores de clientes.';
            break;
        case 'comentarios':
            text = 'En esta parte se muestran los comentarios que los clientes envían a los productos, Ten en cuenta que pueden ser comentarios positivos o negativos. Por motivos de seguridad tú no puedes ver quienes exactamente los envían.';
            break;
        case 'avisos':
            text = 'En esta parte se muestran los avisos que has registrado, puedes modificarlos y borrarlos. Los avisos son para enviar notificaciones a los clientes, en otras palabras, decirles sobre las nuevas funciones, promociones, productos, eventos, o lo que sea que quieras decirles a los clientes.';
            break;
        case 'rarezas':
            text = 'En esta parte se muestran la clasificación de razezas que has registrado, puedes modificarlos y borrarlos. Las rarezas son para indicar a los clientes el nivel de los productos.';
            break;
        case 'universos':
            text = 'En esta parte se muestran la clasificación de universos que has registrado, puedes modificarlos y borrarlos. Los universos son para diferenciar los productos por su serie, marca, saga, o el término "universo" en este caso.';
            break;
        case 'descuentos':
            text = 'En esta parte se muestran las ofertas de descuentos que has registrado, puedes modificarlos y borrarlos. Estos descuentos funcionan de la siguiente manera: se asignan un rango de fechas, y de ese rango se registra el descuento que van a tener los productos durante ese periodo. Es como decir que los productos tengan un descuento de tal porcentaje en tal fecha hasta tal fecha.';
            break;
        case 'accesorios':
            text = 'En esta parte se muestran los accesorios para productos que has registrado, puedes modificarlos y borrarlos. Estos accesorios son para que los ligues con algún producto, por ejemplo; una canasta, con sus colores y todo, y el accesorio probablemente sería una maceta, esta misma con sus colores propios... algo así funciona. Solo que desde "productos" es donde debes buscar estos accesorios para juntarlos.';
            break;
        case 'interacciones':
            text = 'En esta parte se muestran las acciones de los usuarios quienes entran a la aplicación. Esto con el fin de observar la frecuencia con la que las personas ingresan a la aplicación.';
            break;
        default:
            break;
    }
    alertHTML(
        title,
        text,
        icon,
        buttonText
    );
}

function showInputInfo(info) {
    const title = 'Información';
    const icon = 'info';
    const buttonText = 'Cerrar';
    const text = info.length > 0 ? info : '...';
    alertHTML(
        title,
        text,
        icon,
        buttonText
    );
}