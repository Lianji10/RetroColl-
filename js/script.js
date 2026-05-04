// Esperar a que cargue la pagina
document.addEventListener("DOMContentLoaded", function () {
  inicializarNavegacion();
  cargarProductos();
  configurarFormularios();
  configurarFiltros();
  configurarCategorias();
});

// NAVEGACION
function inicializarNavegacion() {
  const enlaces = document.querySelectorAll("[data-page]");

  enlaces.forEach((enlace) => {
    enlace.addEventListener("click", function (evento) {
      evento.preventDefault();
      const destino = this.getAttribute("data-page");
      mostrarPagina(destino);
    });
  });
}

function mostrarPagina(pagina) {
  const todasPaginas = document.querySelectorAll(".page");
  todasPaginas.forEach((p) => {
    p.classList.remove("active");
  });

  const paginaActual = document.querySelector("#" + pagina);
  if (paginaActual) {
    paginaActual.classList.add("active");
  }

  window.scrollTo(0, 0);
}

// Datos de Producos como ejemplo
const listaJuegos = [
  {
    titulo: "Super Mario 64",
    precio: 45,
    categoria: "plataformas",
    plataforma: "Nintendo 64",
    estado: "Con caja",
  },
  {
    titulo: "Marvel vs Capcom",
    precio: 25,
    categoria: "accion",
    plataforma: "Nintendo 64",
    estado: "Con caja",
  },
  {
    titulo: "The Legend of Zelda: Ocarina of Time",
    precio: 65,
    categoria: "rpg",
    plataforma: "Nintendo 64",
    estado: "Como nuevo",
  },
  {
    titulo: "Crash Bandicoot",
    precio: 35,
    categoria: "plataformas",
    plataforma: "PlayStation 1",
    estado: "Buen estado",
  },
  {
    titulo: "Final Fantasy VII",
    precio: 85,
    categoria: "rpg",
    plataforma: "PlayStation 1",
    estado: "Con caja",
  },
  {
    titulo: "Sonic the Hedgehog 2",
    precio: 25,
    categoria: "plataformas",
    plataforma: "Mega Drive",
    estado: "Sin caja",
  },
  {
    titulo: "Street Fighter II Turbo",
    precio: 55,
    categoria: "accion",
    plataforma: "Super Nintendo",
    estado: "Buen estado",
  },
  {
    titulo: "Pokemon Rojo",
    precio: 40,
    categoria: "rpg",
    plataforma: "Game Boy",
    estado: "Sin caja",
  },
  {
    titulo: "Metal Gear Solid",
    precio: 50,
    categoria: "aventura",
    plataforma: "PlayStation 1",
    estado: "Con caja",
  },
  {
    titulo: "Mario Kart 64",
    precio: 48,
    categoria: "carreras",
    plataforma: "Nintendo 64",
    estado: "Buen estado",
  },
  {
    titulo: "Resident Evil 2",
    precio: 70,
    categoria: "aventura",
    plataforma: "PlayStation 1",
    estado: "Como nuevo",
  },
  {
    titulo: "Doom",
    precio: 30,
    categoria: "accion",
    plataforma: "Super Nintendo",
    estado: "Sin caja",
  },
  {
    titulo: "FIFA 98",
    precio: 15,
    categoria: "deportes",
    plataforma: "PlayStation 1",
    estado: "Aceptable",
  },
  {
    titulo: "Donkey Kong Country",
    precio: 42,
    categoria: "plataformas",
    plataforma: "Super Nintendo",
    estado: "Con caja",
  },
  {
    titulo: "Castlevania: Symphony of the Night",
    precio: 120,
    categoria: "aventura",
    plataforma: "PlayStation 1",
    estado: "Como nuevo",
  },
  {
    titulo: "Gran Turismo 2",
    precio: 28,
    categoria: "carreras",
    plataforma: "PlayStation 1",
    estado: "Buen estado",
  },
];

// CARGAR PRODUCTOS
function cargarProductos() {
  cargarProductosHome();
  cargarProductosLista();
}

function cargarProductosHome() {
  const contenedor = document.querySelector("#productosHome");
  if (!contenedor) return;

  contenedor.innerHTML = "";

  const destacados = listaJuegos.slice(0, 10);
  destacados.forEach((juego) => {
    const tarjeta = crearTarjetaJuego(juego);
    contenedor.appendChild(tarjeta);
  });
}

function cargarProductosLista() {
  const contenedor = document.querySelector("#productosLista");
  if (!contenedor) {
    return;
  }

  contenedor.innerHTML = "";

  listaJuegos.forEach((juego) => {
    const tarjeta = crearTarjetaJuego(juego);
    contenedor.appendChild(tarjeta);
  });
}

function crearTarjetaJuego(juego) {
  const tarjeta = document.createElement("div");
  tarjeta.classList.add("product-card");

  tarjeta.innerHTML =
    "<h4>" +
    juego.titulo +
    "</h4>" +
    '<p class="precio">' +
    juego.precio +
    "€</p>" +
    '<span class="categoria">' +
    juego.categoria +
    "</span>" +
    '<p style="color: #888; font-size: 0.9rem; margin-top: 8px;">' +
    juego.plataforma +
    "</p>" +
    '<span class="estado">' +
    juego.estado +
    "</span>";

  return tarjeta;
}

// FORMULARIOS
function configurarFormularios() {
  configurarFormularioLogin();
  configurarFormularioRegistro();
  configurarFormularioVender();
}

function configurarFormularioLogin() {
  const formulario = document.querySelector("#formLogin");
  if (!formulario) {
    return;
  }

  formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Sesión iniciada correctamente");
    mostrarPagina("perfil");
  });
}

function configurarFormularioRegistro() {
  const formulario = document.querySelector("#formRegistro");
  if (!formulario) {
    return;
  }

  formulario.addEventListener("submit", function (e) {
    e.preventDefault();

    const password1 = formulario.querySelector('[name="password"]').value;
    const password2 = formulario.querySelector('[name="password2"]').value;

    if (password1 !== password2) {
      alert("Las contraseñas no coinciden");
      return;
    }

    alert("Cuenta creada correctamente");
    mostrarPagina("login");
  });
}

function configurarFormularioVender() {
  const formulario = document.querySelector("#formVender");
  if (!formulario) return;

  formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Juego publicado correctamente");
    formulario.reset();
    mostrarPagina("productos");
  });
}

// FILTROS
function configurarFiltros() {
  const filtroCategoria = document.querySelector("#filtroCategoria");
  const filtroPrecio = document.querySelector("#filtroPrecio");

  if (filtroCategoria) {
    filtroCategoria.addEventListener("change", aplicarFiltros);
  }

  if (filtroPrecio) {
    filtroPrecio.addEventListener("change", aplicarFiltros);
  }
}

function aplicarFiltros() {
  const categoriaSeleccionada =
    document.querySelector("#filtroCategoria").value;
  const precioSeleccionado = document.querySelector("#filtroPrecio").value;
  const contenedor = document.querySelector("#productosLista");

  if (!contenedor) {
    return;
  }

  let juegosFiltrados = listaJuegos;

  if (categoriaSeleccionada) {
    juegosFiltrados = juegosFiltrados.filter(
      (juego) => juego.categoria === categoriaSeleccionada
    );
  }

  if (precioSeleccionado) {
    const precios = precioSeleccionado.split("-");
    const precioMin = parseInt(precios[0]);
    const precioMax = parseInt(precios[1]);

    juegosFiltrados = juegosFiltrados.filter((juego) => {
      return juego.precio >= precioMin && juego.precio <= precioMax;
    });
  }

  contenedor.innerHTML = "";

  if (juegosFiltrados.length === 0) {
    contenedor.innerHTML =
      '<p style="text-align: center; color: #999;">No se encontraron juegos</p>';
    return;
  }

  juegosFiltrados.forEach((juego) => {
    const tarjeta = crearTarjetaJuego(juego);
    contenedor.appendChild(tarjeta);
  });
}

// CATEGORIAS
function configurarCategorias() {
  const categorias = document.querySelectorAll(".categoria-grande");

  categorias.forEach((categoria) => {
    categoria.addEventListener("click", function () {
      const categoriaSeleccionada = this.getAttribute("data-categoria");
      mostrarPagina("productos");

      const filtro = document.querySelector("#filtroCategoria");
      if (filtro) {
        filtro.value = categoriaSeleccionada;
        aplicarFiltros();
      }
    });
  });
}
