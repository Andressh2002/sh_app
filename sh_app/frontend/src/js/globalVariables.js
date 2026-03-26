const backend = '../../backend/routes/';
const urlProduct = 'productRoute.php';
const urlCategory = 'categoryRoute.php';
const urlColor = 'colorRoute.php';
const urlUser = 'userRoute.php';
const urlOrder = 'orderRoute.php';
const urlDashboard = 'dashboardRoute.php';
const urlHoliday = 'holidayRoute.php';
const urlCard = 'cardRoute.php';
const urlComentary = 'comentaryRoute.php';
const urlNews = 'newsRoute.php';
const urlCarousel = 'carouselRoute.php';
const urlFilter = 'filterRoute.php';
const urlRarity = 'rarityRoute.php';
const urlUniverse = 'universeRoute.php';
const urlDiscount = 'discountRoute.php';
const urlAccesory = 'accesoryRoute.php';

let solicitudAjaxActiva = null;
let cancelarCargaSecuencial = false;

let currentPage = 1;
const itemsPerPage = 10;
