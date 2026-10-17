let peliculas_original = [];
let peliculas_filtradas = [];
let tipoBusqueda = "";
let idPeliCalificar = undefined;
let modificandoCalificacion = false;

/*MANEJO DEL DOM -> DOM: DOCUMENT OBJECT MODEL*/
const contenedor_img = document.querySelector(".contenedor-img");
const dialog = document.getElementById("rating-dialog");
const rating_close = document.getElementById("rating-close");
const list_options = document.querySelector(".list-options");
const reset_filter_icon = document.querySelector(".non-filter-icon");
const find_movie = document.querySelector("#find-movie");
const radios = document.querySelector(".radios");
const reset_find_icon = document.querySelector(".quit-find-icon");
const all_radio_buttons = document.querySelectorAll(".radiobutton");
const rating_send = document.getElementById("rating-send");
const stars = document.querySelector(".stars");

contenedor_img.addEventListener("click", mostrarModal);
contenedor_img.addEventListener("click", agregarFavoritoCalificada);
rating_close.addEventListener("click", cerrarModal);
list_options.addEventListener("click", filtrarPeliculas);
reset_filter_icon.addEventListener("click", resetPeliculas);
find_movie.addEventListener("keyup", buscarPeliculas);
find_movie.addEventListener("click", () => {
  if (find_movie.readOnly) {
    alert(
      "Seleccione un método de busqueda para utilizar la barra de búsqueda 🤓",
    );
  }
});
radios.addEventListener("click", obtenerTipoBusqueda);
reset_find_icon.addEventListener("click", quitarSeleccionRadios);
rating_send.addEventListener("click", agregarCalificacion);

// mostrarPeliculas();

async function cargarPeliculas() {
  try {
    const respuesta = await fetch("../../actions/api_peliculas.php");

    if (!respuesta.ok) {
      throw new Error("Error en la autenticación o en el servidor");
    }

    const datosJSON = await respuesta.json();

    peliculas_original = datosJSON;
    peliculas_filtradas = [...peliculas_original];
    mostrarPeliculas();
  } catch (error) {
    console.error("Error al cargar las películas:", error);
    contenedor_img.innerHTML = `<p style="color: white;">Error al cargar el catálogo.</p>`;
  }
}

function mostrarPeliculas() {
  let html = ``;

  peliculas_filtradas.forEach((peli) => {
    const claseFavorito = peli.favorito ? "favorite-heart" : "non-favorite";
    const claseCalificada = peli.calificada ? "calificated" : "non-calificated";

    html += `
      <div class="contenedor-peli">
        <p class="peli-rating">${peli.rating}</p>
        <a href="info_pelicula.php?id=${peli.id}">
          <img src="../${peli.poster}" alt="${peli.titulo}">
        </a>
        <span class="material-symbols-outlined ${claseFavorito} heart" id="peli-${peli.id}">favorite</span>
        <span class="material-symbols-outlined ${claseCalificada} star" id="peli-${peli.id}">star</span>
      </div>`;
  });

  contenedor_img.innerHTML = html;
}

cargarPeliculas();

function agregarFavoritoCalificada(evento) {
  const target = evento.target;
  if (target.tagName !== "SPAN") return;

  const numero_id = target.id.split("-")[1];
  const pelicula = peliculas_filtradas.find((peli) => peli.id == numero_id);

  // La estrella ya no se procesa aquí, solo el corazón
  if (target.classList.contains("heart")) {
    pelicula.favorito = !pelicula.favorito;
    target.classList.toggle("favorite-heart", pelicula.favorito);
    target.classList.toggle("non-favorite", !pelicula.favorito);
    enviarInteracion(pelicula.id, "favorita");
  }
}

function mostrarModal(evento) {
  let target = evento.target;

  if (target.tagName !== "SPAN" || !target.classList.contains("star")) return;

  idPeliCalificar = target.id.split("-")[1];

  if (target.classList.contains("non-calificated")) {
    modificandoCalificacion = false;
    dialog.showModal();
    document.querySelector("body").classList.toggle("dont-move");
  } else if (target.classList.contains("calificated")) {
    let deseaModificar = confirm(
      "¿Deseas modificar la calificación que le diste a esta película?",
    );

    if (deseaModificar) {
      modificandoCalificacion = true;
      dialog.showModal();
      document.querySelector("body").classList.toggle("dont-move");
    }
  }
}

function cerrarModal() {
  dialog.close();
  document.querySelector("body").classList.toggle("dont-move");
  quitarSeleccionRating();
}

function resetPeliculas() {
  peliculas_filtradas = peliculas_original;
  mostrarPeliculas();
}

function filtrarPeliculas(evento) {
  resetPeliculas();
  let target = evento.target;
  let id_target = target.id;

  if (id_target == "fav") {
    peliculas_filtradas = peliculas_original.filter((peli) => peli.favorito);
  } else if (id_target == "cal") {
    peliculas_filtradas = peliculas_original.filter((peli) => peli.calificada);
  } else if (id_target == "topMejor") {
    peliculas_filtradas = [
      ...peliculas_original.sort((peli, peli2) => peli2.rating - peli.rating),
    ];
  } else if (id_target == "topPeor") {
    console.log("entre");
    peliculas_filtradas = [
      ...peliculas_original.sort((peli, peli2) => peli.rating - peli2.rating),
    ];
  }
  console.log(peliculas_filtradas);
  mostrarPeliculas();
}

function obtenerTipoBusqueda(evento) {
  let target = evento.target;

  tipoBusqueda = target.value;

  if (find_movie.readOnly) {
    find_movie.readOnly = false;
    find_movie.classList.toggle("text-focus");
  }
}

function buscarPeliculas(evento) {
  let target = evento.target;

  if (target.value == "") {
    resetPeliculas();
  } else {
    if (tipoBusqueda == "pelicula") {
      peliculas_filtradas = peliculas_original.filter((peli) =>
        peli.titulo.toLowerCase().startsWith(target.value.toLowerCase()),
      );
    } else if (tipoBusqueda == "actor") {
      peliculas_filtradas = peliculas_original.filter((peli) =>
        peli.elenco.some((persona) =>
          persona.toLowerCase().startsWith(target.value.toLowerCase()),
        ),
      );
    } else if (tipoBusqueda == "genero") {
      peliculas_filtradas = peliculas_original.filter((peli) =>
        peli.genero.some((genero) =>
          genero.toLowerCase().startsWith(target.value.toLowerCase()),
        ),
      );
    } else if (tipoBusqueda == "director") {
      peliculas_filtradas = peliculas_original.filter((peli) =>
        peli.director.toLowerCase().startsWith(target.value.toLowerCase()),
      );
    }

    mostrarPeliculas();
  }
}

function quitarSeleccionRadios() {
  all_radio_buttons.forEach((elemento) => {
    if (elemento.checked) {
      elemento.checked = false;
    }
  });

  if (!find_movie.readOnly) {
    find_movie.readOnly = true;
    find_movie.classList.toggle("text-focus");
    find_movie.value = "";
    resetPeliculas();
  }
}

function obtenerRatingPelicula(pelicula) {
  return (
    pelicula.calificaciones.reduce(
      (acumulador, calificacion) => acumulador + calificacion,
      0,
    ) / pelicula.calificaciones.length
  );
}

function agregarCalificacion() {
  let ratingSeleccionado = 0;

  stars.childNodes.forEach((elemento) => {
    if (elemento.checked) {
      ratingSeleccionado = parseFloat(elemento.value);
    }
  });

  if (ratingSeleccionado > 0) {
    enviarInteracion(idPeliCalificar, "calificada", ratingSeleccionado);
    cargarPeliculas();
    // let peliculaEncontrada = peliculas_original.find(
    //   (peli) => peli.id == idPeliCalificar,
    // );

    // if (modificandoCalificacion) {
    //   let ultimaPosicion = peliculaEncontrada.calificaciones.length - 1;
    //   peliculaEncontrada.calificaciones[ultimaPosicion] = ratingSeleccionado;
    // } else {
    //   peliculaEncontrada.calificaciones.push(ratingSeleccionado);
    //   peliculaEncontrada.calificada = true;
    // }

    // peliculas_filtradas = peliculas_original;
    // mostrarPeliculas();
  }

  cerrarModal();
}

async function enviarInteracion(id_pelicula, tipo = null, calificacion = null) {
  try {
    console.log(id_pelicula);
    let objeto_enviar = {
      id_pelicula: id_pelicula,
      tipo: tipo,
      calificacion: calificacion,
    };
    if (tipo == "calificada") {
      objeto_enviar.tipo = "calificada";
      objeto_enviar.calificacion = calificacion;
    } else if (tipo == "favorita") {
      objeto_enviar.tipo = "favorita";
    }
    console.log(JSON.stringify(objeto_enviar));
    await fetch("../../actions/api_interaciones.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(objeto_enviar),
    })
      .then((response) => response.json())
      .then((respuesta) => {
        console.log("Respuesta del servidor", respuesta);
      })
      .catch((error) => {
        console.error("Error por parte del servidor: ", error);
      });
  } catch (error) {
    console.error("Ha ocurrido un error en la solicitud", error);
  }
}

function quitarSeleccionRating() {
  stars.childNodes.forEach((elemento) => {
    if (elemento.checked) {
      elemento.checked = false;
    }
  });
}
